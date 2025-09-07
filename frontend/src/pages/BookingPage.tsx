import { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { useNavigate } from 'react-router-dom'
import { useVenues, useTables, useBook } from '@/lib/hooks'
import { toast } from '@/store/toast'

export default function BookingPage() {
  const { t } = useTranslation()
  const navigate = useNavigate()
  const [selectedVenue, setSelectedVenue] = useState<number | undefined>()
  const [date, setDate] = useState<string>('2025-01-15')
  const [selectedTableId, setSelectedTableId] = useState<number | undefined>()
  const [selectedSlot, setSelectedSlot] = useState<{ start_time: string; end_time: string } | undefined>()
  const { data: venues, isLoading: vLoading } = useVenues()
  const { data: tables, isLoading: tLoading } = useTables(selectedVenue, date)
  const book = useBook()

  async function handleBook() {
    if (!selectedTableId || !selectedSlot) {
      toast.error('Select table and slot')
      return
    }
    try {
      const resp = await book.mutateAsync({
        table_id: selectedTableId,
        start_time: `${date}T${selectedSlot.start_time}:00Z`,
        end_time: `${date}T${selectedSlot.end_time}:00Z`,
      }) as any
      const id = resp?.id || resp?.data?.id
      toast.success('Booking created')
      navigate(`/booking/confirm?id=${id}`)
    } catch (e: any) {
      toast.error(e?.data?.message || 'Booking failed')
    }
  }

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-bold">{t('home.book_table')}</h1>

      <div className="bg-card border border-white/5 rounded-xl p-4 space-y-3">
        <div className="flex flex-wrap gap-2">
          {vLoading && <span className="text-text-muted">Loading venues…</span>}
          {venues?.data?.map((v) => (
            <button
              key={v.id}
              onClick={() => { setSelectedVenue(v.id); setSelectedTableId(undefined); setSelectedSlot(undefined) }}
              className={`px-3 py-1 rounded-md border ${selectedVenue === v.id ? 'border-primary text-primary' : 'border-white/10 text-text-muted'}`}
            >
              {v.name}
            </button>
          ))}
        </div>
        <div>
          <input
            type="date"
            value={date}
            onChange={(e) => setDate(e.target.value)}
            className="bg-background border border-white/10 px-3 py-2 rounded-md"
          />
        </div>
      </div>

      <div className="bg-card border border-white/5 rounded-xl p-4">
        {tLoading && <div className="text-text-muted">Loading tables…</div>}
        <div className="grid md:grid-cols-2 gap-3">
          {tables?.data?.map((t) => (
            <div
              key={t.id}
              className={`border rounded-lg p-3 ${selectedTableId === t.id ? 'border-primary' : 'border-white/10'}`}
            >
              <div className="flex items-center justify-between">
                <div className="font-semibold">Table {t.table_number} • {t.table_type}</div>
                <button
                  onClick={() => setSelectedTableId(t.id)}
                  className={`text-xs px-2 py-1 rounded border ${selectedTableId === t.id ? 'border-primary text-primary' : 'border-white/10 text-text-muted'}`}
                >
                  {selectedTableId === t.id ? 'Selected' : 'Select'}
                </button>
              </div>
              <div className="text-text-muted text-sm">₦{t.hourly_rate.toLocaleString()} / hr</div>
              <div className="mt-2 flex flex-wrap gap-2">
                {t.available_slots.slice(0, 6).map((s, idx) => (
                  <button
                    key={idx}
                    onClick={() => { setSelectedTableId(t.id); setSelectedSlot({ start_time: s.start_time, end_time: s.end_time }) }}
                    className={`text-xs border rounded px-2 py-1 ${selectedTableId === t.id && selectedSlot?.start_time === s.start_time ? 'border-primary text-primary' : 'border-white/10 text-text-muted'}`}
                  >
                    {s.start_time} - {s.end_time}
                  </button>
                ))}
              </div>
            </div>
          ))}
        </div>
        <div className="mt-4 flex justify-end">
          <button
            onClick={() => void handleBook()}
            disabled={book.isPending}
            className="bg-primary text-black px-4 py-2 rounded-md disabled:opacity-50"
          >
            {book.isPending ? 'Booking…' : t('booking.booking_confirm')}
          </button>
        </div>
      </div>
    </section>
  )
}
