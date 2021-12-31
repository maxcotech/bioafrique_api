<?php
namespace App\Interfaces;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\TransactionDetails;
use App\Services\WalletServices\Utilities\TransferRecipient;

interface Wallet{
    public function depositLockedOrderFund(
        $amount,SenderObject $sender, LockDetails $lock_details,
        TransactionDetails $trx_details = null
    );

    public function getTotalUnLockedCredits();

    public function getTotalDebits();

    public function getTotalLockedCredits();

    public function getTotalPendingWithdrawal();

    public function getTotalAccountBalance();

    public function withdrawFund($amount);

    public function transferFund($amount,TransferRecipient $recipient);

}