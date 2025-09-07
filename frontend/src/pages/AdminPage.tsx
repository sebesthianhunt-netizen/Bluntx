import { useState } from 'react'
import { useCreateVenue, useCreateTable } from '@/lib/hooks'

export default function AdminPage() {
  const [venueName, setVenueName] = useState('')
  const [venueAddress, setVenueAddress] = useState('')
  const [venueId, setVenueId] = useState<number | ''>('')
  const [tableNumber, setTableNumber] = useState<number | ''>('')
  const [tableType, setTableType] = useState('Standard')
  const [hourlyRate, setHourlyRate] = useState<number | ''>('')
  const createVenue = useCreateVenue()
  const createTable = useCreateTable()

  return (
    <section className="space-y-6 max-w-xl">
      <h1 className="text-2xl font-bold">Admin Dashboard</h1>

      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <h2 className="font-semibold">Create Venue</h2>
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Name" value={venueName} onChange={(e) => setVenueName(e.target.value)} />
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Address" value={venueAddress} onChange={(e) => setVenueAddress(e.target.value)} />
        <button
          className="px-4 py-2 rounded bg-primary text-white disabled:opacity-50"
          disabled={!venueName || createVenue.isPending}
          onClick={() => createVenue.mutate({ name: venueName, address: venueAddress || undefined })}
        >
          {createVenue.isPending ? 'Creating…' : 'Create Venue'}
        </button>
        {createVenue.isSuccess && <p className="text-green-500 text-sm">Venue created.</p>}
      </div>

      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <h2 className="font-semibold">Create Table</h2>
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Venue ID" value={venueId} onChange={(e) => setVenueId(e.target.value ? Number(e.target.value) : '')} />
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Table Number" value={tableNumber} onChange={(e) => setTableNumber(e.target.value ? Number(e.target.value) : '')} />
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Table Type" value={tableType} onChange={(e) => setTableType(e.target.value)} />
        <input className="w-full bg-transparent border rounded px-3 py-2" placeholder="Hourly Rate (NGN)" value={hourlyRate} onChange={(e) => setHourlyRate(e.target.value ? Number(e.target.value) : '')} />
        <button
          className="px-4 py-2 rounded bg-primary text-white disabled:opacity-50"
          disabled={!venueId || !tableNumber || !hourlyRate || createTable.isPending}
          onClick={() =>
            createTable.mutate({
              venueId: Number(venueId),
              data: { table_number: Number(tableNumber), table_type: tableType, hourly_rate: Number(hourlyRate) },
            })
          }
        >
          {createTable.isPending ? 'Creating…' : 'Create Table'}
        </button>
        {createTable.isSuccess && <p className="text-green-500 text-sm">Table created.</p>}
      </div>
    </section>
  )
}
