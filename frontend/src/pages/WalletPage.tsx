import { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { useWallet } from '@/lib/hooks'
import { FundModal, WithdrawModal, TransferModal } from '@/components/WalletModals'
import { Link } from 'react-router-dom'

export default function WalletPage() {
  const { t } = useTranslation()
  const { data, isLoading, isError } = useWallet()
  const [showFund, setShowFund] = useState(false)
  const [showWithdraw, setShowWithdraw] = useState(false)
  const [showTransfer, setShowTransfer] = useState(false)

  return (
    <section className="space-y-3">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">{t('home.wallet_balance')}</h1>
        <Link to="/wallet/history" className="text-text-muted hover:text-primary text-sm">{t('wallet.transaction_history')}</Link>
      </div>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        {isError && <div className="text-danger">{t('error.server_error')}</div>}
        {data && (
          <div className="grid md:grid-cols-3 gap-4">
            <Stat label="Cash" value={`₦${data.cash_balance.toLocaleString()}`} />
            <Stat label="Points" value={data.points_balance.toLocaleString()} />
            <Stat label="Escrow" value={`₦${data.escrow_balance.toLocaleString()}`} />
          </div>
        )}
      </div>
      <div className="grid md:grid-cols-3 gap-4">
        <div className="bg-card border border-white/5 rounded-xl p-6">
          <h2 className="font-semibold mb-2">{t('wallet.fund_wallet')}</h2>
          <button onClick={() => setShowFund(true)} className="bg-primary text-black px-4 py-2 rounded-md">{t('home.wallet_topup')}</button>
        </div>
        <div className="bg-card border border-white/5 rounded-xl p-6">
          <h2 className="font-semibold mb-2">{t('wallet.withdraw_cash')}</h2>
          <button onClick={() => setShowWithdraw(true)} className="bg-primary text-black px-4 py-2 rounded-md">{t('home.wallet_withdraw')}</button>
        </div>
        <div className="bg-card border border-white/5 rounded-xl p-6">
          <h2 className="font-semibold mb-2">{t('wallet.transfer_user')}</h2>
          <button onClick={() => setShowTransfer(true)} className="bg-primary text-black px-4 py-2 rounded-md">{t('wallet.transfer_user')}</button>
        </div>
      </div>

      {showFund && <FundModal onClose={() => setShowFund(false)} />}
      {showWithdraw && <WithdrawModal onClose={() => setShowWithdraw(false)} />}
      {showTransfer && <TransferModal onClose={() => setShowTransfer(false)} />}
    </section>
  )
}

function Stat(props: { label: string; value: string | number }) {
  return (
    <div className="rounded-lg border border-white/5 p-4">
      <div className="text-text-muted text-sm">{props.label}</div>
      <div className="text-2xl font-bold">{props.value}</div>
    </div>
  )
}
