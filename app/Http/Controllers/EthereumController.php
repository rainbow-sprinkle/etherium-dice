<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Web3\Web3;
use Web3\Utils;

class EthereumController extends Controller
{
    public function testConnection()
    {
        $web3 = new Web3('https://dry-frequent-grass.ethereum-goerli.quiknode.pro/');
        $web3->eth->blockNumber(function ($error, $result) {
            if ($error !== null) {
                // Handle the error.
                echo "Error: " . $error->getMessage();
            } else {
                // Success! Do something with the block number.
                echo "The latest block number is: " . $result->toString();
            }
        });
    }
     }