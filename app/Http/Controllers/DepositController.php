<?php
namespace App\Http\Controllers;

use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Illuminate\Http\Request as AppRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;


class DepositController extends Controller
{
    public function showalldeposits()
    {
        $user = Auth::user();
        $deposits = Transaction::where('to_address', $user->eth_address)->get();
        return view('deposit', ['deposits' => $deposits]);
    }

    public function checkForDeposits(AppRequest $request)
    {
        // Set up Web3 provider and connection
        $httpProvider = new HttpProvider(new HttpRequestManager('https://dry-frequent-grass.ethereum-goerli.quiknode.pro/'));
        $web3 = new Web3($httpProvider);
        // Get all users from the database
        $users = User::all();

        foreach ($users as $user) {
            $address = $user->eth_address;
            // Get transactions for user's Ethereum address
            $transactions = $web3->eth->getTransactionByAddress($address);
            foreach ($transactions as $transaction) {
                $toAddress = $transaction->to;
                $amount = $web3->fromWei($transaction->value, 'ether');
                $existingTransaction = Transaction::where('tx_hash', $transaction->hash)->first();
                if (!$existingTransaction) {
                    $newTransaction = new Transaction();
                    $newTransaction->user_id = $user->id;
                    $newTransaction->tx_hash = $transaction->hash;
                    $newTransaction->to_address = $toAddress;
                    $newTransaction->amount = $amount;
                    $newTransaction->save();
                }
            }
        }
    }
}
