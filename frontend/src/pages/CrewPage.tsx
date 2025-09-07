import { useTranslation } from 'react-i18next'
import { useCrews } from '@/lib/hooks'
import { useState } from 'react'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { toast } from '@/store/toast'

export default function CrewPage() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const { data, isLoading } = useCrews()
  const [name, setName] = useState('')
  const [crewId, setCrewId] = useState('')

  async function createCrew() {
    try {
      await apiFetch('/crew', { method: 'POST', token, body: { name } })
      toast.success('Crew created')
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to create crew')
    }
  }

  async function joinCrew() {
    try {
      await apiFetch(`/crew/${crewId}/join`, { method: 'POST', token })
      toast.success('Joined crew')
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to join crew')
    }
  }

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('crew.crew_home')}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        <div className="grid md:grid-cols-2 gap-3">
          {data?.data?.map((c) => (
            <div key={c.id} className="border border-white/10 rounded-lg p-4">
              <div className="font-semibold">{c.name}</div>
              <div className="text-text-muted text-sm">{c.description}</div>
              <div className="text-xs uppercase tracking-wide mt-1">{c.is_public ? 'Public' : 'Private'}</div>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <div className="font-semibold">Create Crew</div>
        <div className="flex gap-2">
          <input className="flex-1 bg-background border border-white/10 rounded px-3 py-2" placeholder="Name" value={name} onChange={(e) => setName(e.target.value)} />
          <button onClick={() => void createCrew()} className="bg-primary text-black px-4 py-2 rounded-md">Create</button>
        </div>
        <div className="font-semibold mt-4">Join Crew</div>
        <div className="flex gap-2">
          <input className="flex-1 bg-background border border-white/10 rounded px-3 py-2" placeholder="Crew ID" value={crewId} onChange={(e) => setCrewId(e.target.value)} />
          <button onClick={() => void joinCrew()} className="bg-primary text-black px-4 py-2 rounded-md">Join</button>
        </div>
      </div>
    </section>
  )
}
