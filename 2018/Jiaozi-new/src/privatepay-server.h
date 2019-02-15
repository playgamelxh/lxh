// Copyright (c) 2014-2017 The Dash Core developers
// Copyright (c) 2017-2017 The OIOCoin Core developers
// Distributed under the MIT/X11 software license, see the accompanying
// file COPYING or http://www.opensource.org/licenses/mit-license.php.

#ifndef PRIVATEPAYSERVER_H
#define PRIVATEPAYSERVER_H

#include "net.h"
#include "privatepay.h"

class CPrivatePayServer;

// The main object for accessing mixing
extern CPrivatePayServer privatePayServer;

/** Used to keep track of current status of mixing pool
 */
class CPrivatePayServer : public CPrivatePayBase
{
private:
    mutable CCriticalSection cs_privatepay;

    // Mixing uses collateral transactions to trust parties entering the pool
    // to behave honestly. If they don't it takes their money.
    std::vector<CTransaction> vecSessionCollaterals;

    bool fUnitTest;

    /// Add a clients entry to the pool
    bool AddEntry(const CPrivatePayEntry& entryNew, PoolMessage& nMessageIDRet);
    /// Add signature to a txin
    bool AddScriptSig(const CTxIn& txin);

    /// Charge fees to bad actors (Charge clients a fee if they're abusive)
    void ChargeFees();
    /// Rarely charge fees to pay miners
    void ChargeRandomFees();

    /// Check for process
    void CheckPool();

    void CreateFinalTransaction();
    void CommitFinalTransaction();

    /// Is this nDenom and txCollateral acceptable?
    bool IsAcceptableDenomAndCollateral(int nDenom, CTransaction txCollateral, PoolMessage &nMessageIDRet);
    bool CreateNewSession(int nDenom, CTransaction txCollateral, PoolMessage &nMessageIDRet);
    bool AddUserToExistingSession(int nDenom, CTransaction txCollateral, PoolMessage &nMessageIDRet);
    /// Do we have enough users to take entries?
    bool IsSessionReady() { return (int)vecSessionCollaterals.size() >= CPrivatePay::GetMaxPoolTransactions(); }

    /// Check that all inputs are signed. (Are all inputs signed?)
    bool IsSignaturesComplete();
    /// Check to make sure a given input matches an input in the pool and its scriptSig is valid
    bool IsInputScriptSigValid(const CTxIn& txin);
    /// Are these outputs compatible with other client in the pool?
    bool IsOutputsCompatibleWithSessionDenom(const std::vector<CTxDSOut>& vecTxDSOut);

    // Set the 'state' value, with some logging and capturing when the state changed
    void SetState(PoolState nStateNew);

    /// Relay mixing Messages
    void RelayFinalTransaction(const CTransaction& txFinal);
    void PushStatus(CNode* pnode, PoolStatusUpdate nStatusUpdate, PoolMessage nMessageID);
    void RelayStatus(PoolStatusUpdate nStatusUpdate, PoolMessage nMessageID = MSG_NOERR);
    void RelayCompletedTransaction(PoolMessage nMessageID);

    void SetNull();

public:
    CPrivatePayServer() :
        fUnitTest(false) { SetNull(); }

    void ProcessMessage(CNode* pfrom, std::string& strCommand, CDataStream& vRecv);

    void CheckTimeout();
    void CheckForCompleteQueue();
};

void ThreadCheckPrivatePayServer();

#endif
