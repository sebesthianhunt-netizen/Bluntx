import { useParams } from 'react-router-dom'
import { useTournamentBracket } from '@/lib/hooks'

export default function TournamentDetailsPage() {
  const params = useParams()
  const id = Number(params.id)
  const { data, isLoading } = useTournamentBracket(id)

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Tournament #{id}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        {data && (
          <div className="space-y-3">
            {(data.rounds || []).map((r: any) => (
              <div key={r.round} className="border border-white/10 rounded-lg p-3">
                <div className="font-semibold mb-2">Round {r.round}</div>
                <div className="grid md:grid-cols-2 gap-2">
                  {r.matches.map((m: any) => (
                    <div key={m.match_id} className="border border-white/10 rounded p-2 text-sm">
                      <div>Match #{m.match_id}</div>
                      <div>A: {m.player_a_id} vs B: {m.player_b_id}</div>
                      <div className="text-text-muted">{m.status}</div>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  )
}
