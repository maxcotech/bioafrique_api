<?php
namespace App\Interfaces;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\TransactionDetails;

interface Wallet{
    public function depositLockedOrderFund(
        $amount,SenderObject $sender, LockDetails $lock_details,
        TransactionDetails $trx_details = null
    );

}