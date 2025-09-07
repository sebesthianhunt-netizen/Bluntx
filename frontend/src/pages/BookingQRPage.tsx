import { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { apiFetch } from '@/lib/apiClient'

export default function BookingQRPage() {
  const [params] = useSearchParams()
  const id = params.get('id')
  const [qr, setQr] = useState<string | null>(null)

  useEffect(() => {
    let active = true
    async function load() {
      if (!id) return
      const data = await apiFetch<{ qr_code: string }>(`/booking/qr/${id}`)
      if (active) setQr(data.qr_code)
    }
    void load()
    return () => { active = false }
  }, [id])

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Booking QR</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        {qr ? (
          <img src={qr} alt="QR" className="w-64 h-64" />
        ) : (
          <div className="text-text-muted">Loading…</div>
        )}
      </div>
    </section>
  )
}
