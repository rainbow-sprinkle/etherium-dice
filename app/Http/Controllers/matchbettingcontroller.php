<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrBet;
use App\Models\user;
use App\Models\House;
use App\Models\Betslip;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateBetslipRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class matchbettingController extends Controller
{

    public function createBetslip(Request $request)
{
    $this->validate($request, [
        'name' => 'required|string|max:255',
        'odd_one' => 'required|numeric',
        'odd_two' => 'required|numeric',
        'odd_three' => 'nullable|numeric',
        'description' => 'nullable|string|max:500',
        'picture' => 'nullable|image|max:2048',
        'freeze_time' => 'required|date' // freeze time is required and must be a valid date string

    ]);

    // Handle the file upload
    $path = null; // initialize as null
    if ($request->hasFile('picture')) {
        $picture = $request->file('picture');
        // generate a new filename. getClientOriginalExtension() for the file extension
        $filename = 'picture_' . time() . '.' . $picture->getClientOriginalExtension();
        // Move the file to the 'public/images' directory under your project root
        $picture->move(public_path('images'), $filename);
        // Define $path as the location of the uploaded file
        $path = 'images/' . $filename; // if picture is provided, $path will be updated

    }

    $betslip = new Betslip();
    $betslip->name = $request->input('name');
    $betslip->odd_one = $request->input('odd_one');
    $betslip->odd_two = $request->input('odd_two');
    $betslip->odd_three = $request->input('odd_three');
    $betslip->description = $request->input('description');
    $betslip->picture = $path;
    $betslip->freeze_time = Carbon::parse($request->input('freeze_time'));
    $betslip->save();


    return back()->with('success', 'Betslip created successfully');
}
public function placeBet(Request $request, Betslip $betslip)
{
        // Check if Betslip is open
        if ($betslip->status !== 'open') {
            return response()->json([
                'message' => 'This Betslip is not accepting bets'
            ], 400);
        }
    // retrieve the max bet
    $house = House::where('name', 'PrBettax')->firstOrFail();
    $maxBet = $house->max_bet;

    // retrieve the current user
    $user = auth()->user();

    // Calculate total bet amount of the user for the current betslip
    $totalBetAmount = PrBet::where('user_id', $user->id)
                            ->where('betslip_id', $betslip->id)
                            ->where('status', 'pending')
                            ->sum('bet_amount');
    
    $newBetAmount = $request->input('bet_amount');
    $totalBetAmount += $newBetAmount;
    // Validate
    $this->validate($request, [
        'selected_odd' => ['required']  // specify that a selected odd is required
    ]);
    // Validate
    if ($totalBetAmount > $maxBet) {
        return response()->json([
            'message' => 'Total bet amount exceeded the maximum limit'
        ], 400);
    }

    if ($user->coins < $newBetAmount) {
        return response()->json([
            'message' => 'Insufficient funds'
        ], 400);
    }

    // Validation passed, proceed with placing the bet

    // Deduct bet amount
    $user->coins -= $newBetAmount;
    $user->save();

    // Add the bet amount to the house
    $house->coins += $newBetAmount;
    $house->save();

    $selectedOdd = $request->input('selected_odd');
    $betOdds = $betslip->{$selectedOdd};

    $bet = new PrBet();
    $bet->user_id = $user->id;
    $bet->betslip_id = $betslip->id;
    $bet->bet_amount = $newBetAmount;
    $bet->selected_odd = $selectedOdd;  // Saving the name of the odd
    $bet->odds = $betOdds; 
    $bet->status = 'pending';  // Or whatever status you want to set
    $bet->save();

    return response()->json([
        'message' => 'Bet placed successfully',
        'updated_coins' => $user->coins  // Return the updated coin balance
    ], 200);
}
    public function index()
{

    $house = House::where('name', 'PrBettax')->first(); 
    $maxBet = $house->max_bet;
    $betslips = Betslip::whereIn('status', ['open', 'frozen'])->get();



    
    return view('matchbetting', compact('betslips', 'maxBet'));
}
public function closeBetslip(Request $request, Betslip $betslip)
{
    $request->validate([
        'winning_odd' => 'required|in:odd_one,odd_two,odd_three'
    ]);

    DB::transaction(function () use ($request, $betslip) {
        $winning_odd_name = $request->input('winning_odd');

        $betslip->status = 'closed';
        $betslip->winning_odd = $winning_odd_name;
        $betslip->save();

        // Get all the bets that were placed on this betslip
        $bets = PrBet::where('betslip_id', $betslip->id)->get();

        // Get the PrBettax house
        $house = House::where('name', 'PrBettax')->first();

        foreach ($bets as $bet) {
            Log::info('Selected odd for bet: ' . $bet->selected_odd);
            Log::info('Winning odd: ' . $winning_odd_name);
            
            if ($bet->selected_odd == $winning_odd_name) {
                $user = User::find($bet->user_id);
                Log::info('User coins before update: ' . $user->coins);
                $winnings = $bet->bet_amount * $bet->odds;  // calculate the winnings using the odds at the time the bet was placed

                // Deduct winnings from the house
                $house->coins -= $winnings;

                $user->coins += $winnings;
                Log::info('User coins after update: ' . $user->coins);
                $user->save();
                $house->save();
                Log::info('Coins updated for user: ' . $user->id);

                $bet->status = 'won';
                $bet->save();
            } else {
                $bet->status = 'lost';
                $bet->save();
                Log::info('Condition not met for bet: ' . $bet->id);
            }
        }
    });


    return redirect()->route('admin.betslip');  // assuming you have a route named 'admin.betslip'
}
public function freezeBetslip(Request $request, Betslip $betslip)
{

$betslip->status = 'frozen';
$betslip->save();

return redirect()->route('admin.betslip');  // assuming you have a route named 'admin.betslip'
}
}