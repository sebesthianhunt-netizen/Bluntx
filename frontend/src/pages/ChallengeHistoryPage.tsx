import { useChallengeHistory } from '@/lib/hooks'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { toast } from '@/store/toast'

export default function ChallengeHistoryPage() {
  const { data, isLoading, isError } = useChallengeHistory()
  const token = useSessionStore((s) => s.accessToken)

  async function act(id: number, action: 'accept' | 'decline') {
    try {
      await apiFetch(`/challenge/${id}/${action}`, { method: 'POST', token })
      toast.success(`Challenge ${action}ed`)
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed')
    }
  }

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Challenge History</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        {isError && <div className="text-danger">Failed to load</div>}
        <div className="space-y-2">
          {data?.data?.map((c) => (
            <div key={c.id} className="border border-white/10 rounded-lg p-3">
              <div className="flex items-center justify-between">
                <div>ID #{c.id}</div>
                <div className="text-xs uppercase tracking-wide">{c.status}</div>
              </div>
              <div className="text-text-muted text-sm">Stake ₦{c.stake_amount.toLocaleString()}</div>
              {c.status === 'pending' && (
                <div className="mt-2 flex gap-2">
                  <button onClick={() => void act(c.id, 'accept')} className="bg-primary text-black rounded px-3 py-1 text-sm">Accept</button>
                  <button onClick={() => void act(c.id, 'decline')} className="border border-white/10 rounded px-3 py-1 text-sm">Decline</button>
                </div>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
