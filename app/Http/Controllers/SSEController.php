<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\DiceGame;
use Illuminate\Support\Facades\DB;

class SSEController extends Controller
{

  //  public function diceGamesSSE()
    {
        $response = new StreamedResponse(function () {
            // Initialize the last sent game ID to 0
            static $lastSentGameId = 0;
    
            // Turn on output buffering
            ob_start();
    
            while (true) {
                try {
                    // Fetch the latest game whose ID is greater than the last sent game's ID
                    $newGame = DiceGame::select('dice_games.*', 'users.name as user_name')
                        ->join('users', 'dice_games.user_id', '=', 'users.id')
                        ->where('dice_games.id', '>', $lastSentGameId)
                        ->orderBy('dice_games.id', 'desc')
                        ->first();
    
                    if ($newGame) {
                        // Update the last sent game ID
                        $lastSentGameId = $newGame->id;
    
                        echo 'data: ' . json_encode($newGame) . "\n\n";
    
                        // Flush the output buffer if there's something to flush
                        if (ob_get_length()) {
                            ob_flush();
                        }
    
                        // Flush system output buffer
                        flush();
                    }
    
                    // Sleep for a while before checking for new data
                    sleep(1);
                } catch (Exception $e) {
                    sleep(1);
                }
            }
        });
    
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
    
        return $response;
    }
}