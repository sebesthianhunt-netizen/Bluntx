import { useTranslation } from 'react-i18next'
import { useTournaments } from '@/lib/hooks'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { toast } from '@/store/toast'

export default function TournamentsPage() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const { data, isLoading } = useTournaments()

  async function register(id: number) {
    try {
      await apiFetch(`/tournaments/${id}/register`, { method: 'POST', token })
      toast.success('Registered')
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to register')
    }
  }

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('tournament.tournament_list')}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        <div className="grid md:grid-cols-2 gap-3">
          {data?.data?.map((t) => (
            <div key={t.id} className="border border-white/10 rounded-lg p-4">
              <div className="font-semibold">{t.name}</div>
              <div className="text-text-muted text-sm">₦{t.entry_fee.toLocaleString()} • prize ₦{t.prize_pool.toLocaleString()}</div>
              <div className="text-xs uppercase tracking-wide mt-1">{t.status}</div>
              <div className="mt-2 flex gap-2">
                <a href={`/tournaments/${t.id}`} className="text-sm border border-white/10 rounded px-3 py-1">{t('tournament.bracket_view')}</a>
                <button onClick={() => void register(t.id)} className="text-sm bg-primary text-black rounded px-3 py-1">{t('tournament.register')}</button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
