import { useWalletHistory } from '@/lib/hooks'

export default function WalletHistoryPage() {
  const { data, isLoading, isError } = useWalletHistory()
  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Wallet History</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        {isError && <div className="text-danger">Failed to load</div>}
        <div className="space-y-2">
          {data?.data?.map((tx: any) => (
            <div key={tx.id} className="border border-white/10 rounded-lg p-3">
              <div className="flex items-center justify-between">
                <div className="font-semibold uppercase text-xs">{tx.type}</div>
                <div className="text-xs">{new Date(tx.created_at).toLocaleString()}</div>
              </div>
              <div className="text-text-muted text-sm">₦{Number(tx.amount).toLocaleString()} • {tx.status}</div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
