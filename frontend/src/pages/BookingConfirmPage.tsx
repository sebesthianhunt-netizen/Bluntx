import { useSearchParams } from 'react-router-dom'

export default function BookingConfirmPage() {
  const [params] = useSearchParams()
  const id = params.get('id')
  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Booking Confirmed</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        <div>Booking ID: {id}</div>
      </div>
    </section>
  )
}
