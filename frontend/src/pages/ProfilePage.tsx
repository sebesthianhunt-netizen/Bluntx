import { useEffect, useState } from 'react'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { toast } from '@/store/toast'

export default function ProfilePage() {
  const token = useSessionStore((s) => s.accessToken)
  const [loading, setLoading] = useState(true)
  const [form, setForm] = useState<{ nickname?: string; email?: string; avatar_url?: string; bio?: string }>({})

  useEffect(() => {
    let active = true
    async function load() {
      try {
        const r = await apiFetch<{ id: number; nickname?: string; email?: string; avatar_url?: string; bio?: string }>(`/user/profile`, { token })
        if (active) setForm({ nickname: r.nickname, email: r.email, avatar_url: r.avatar_url, bio: (r as any).bio })
      } catch {}
      setLoading(false)
    }
    void load()
    return () => { active = false }
  }, [token])

  async function save() {
    try {
      await apiFetch(`/user/profile`, { method: 'PATCH', body: form, token })
      toast.success('Profile updated')
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to update')
    }
  }

  if (loading) return <div className="text-text-muted">Loading…</div>

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Profile</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <Input label="Nickname" value={form.nickname || ''} onChange={(v) => setForm((s) => ({ ...s, nickname: v }))} />
        <Input label="Email" value={form.email || ''} onChange={(v) => setForm((s) => ({ ...s, email: v }))} />
        <Input label="Avatar URL" value={form.avatar_url || ''} onChange={(v) => setForm((s) => ({ ...s, avatar_url: v }))} />
        <Input label="Bio" value={form.bio || ''} onChange={(v) => setForm((s) => ({ ...s, bio: v }))} />
        <button onClick={() => void save()} className="bg-primary text-black px-4 py-2 rounded-md">Save</button>
      </div>
    </section>
  )
}

function Input(props: { label: string; value: string; onChange: (v: string) => void }) {
  return (
    <label className="block text-sm">
      <span className="mb-1 block">{props.label}</span>
      <input value={props.value} onChange={(e) => props.onChange(e.target.value)} className="w-full rounded-md bg-background border border-white/10 px-3 py-2" />
    </label>
  )
}
