import { useState } from 'react'
import { useCheckin, useCheckout } from '@/lib/hooks'

export default function AttendantPage() {
  const [bookingId, setBookingId] = useState<number | ''>('')
  const checkin = useCheckin()
  const checkout = useCheckout()
  return (
    <section className="space-y-3 max-w-xl">
      <h1 className="text-2xl font-bold">Attendant Console</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Booking ID" value={bookingId} onChange={(e) => setBookingId(e.target.value ? Number(e.target.value) : '')} />
        <div className="flex gap-3">
          <button
            className="px-4 py-2 rounded bg-primary text-white disabled:opacity-50"
            disabled={!bookingId || checkin.isPending}
            onClick={() => checkin.mutate(Number(bookingId))}
          >
            {checkin.isPending ? 'Checking in…' : 'Check In'}
          </button>
          <button
            className="px-4 py-2 rounded bg-secondary text-white disabled:opacity-50"
            disabled={!bookingId || checkout.isPending}
            onClick={() => checkout.mutate(Number(bookingId))}
          >
            {checkout.isPending ? 'Checking out…' : 'Check Out'}
          </button>
        </div>
        {(checkin.isSuccess || checkout.isSuccess) && <p className="text-green-500 text-sm">Success.</p>}
      </div>
    </section>
  )
}
