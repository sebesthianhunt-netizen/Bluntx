import { useEffect, useState } from 'react'
import { apiFetch } from '@/lib/apiClient'

export default function MerchPage() {
  const [items, setItems] = useState<any[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    let active = true
    async function load() {
      try {
        const r = await apiFetch<{ data: any[] }>(`/merch`)
        if (active) setItems(r.data || [])
      } finally {
        setLoading(false)
      }
    }
    void load()
    return () => { active = false }
  }, [])

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Merch Store</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {loading && <div className="text-text-muted">Loading…</div>}
        <div className="grid md:grid-cols-3 gap-3">
          {items.map((it) => (
            <div key={it.id} className="border border-white/10 rounded-lg p-4">
              <div className="font-semibold">{it.name}</div>
              <div className="text-text-muted text-sm">₦{Number(it.price).toLocaleString()}</div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
