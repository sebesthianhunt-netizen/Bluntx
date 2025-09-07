import { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'

export default function ChallengePage() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const [opponentId, setOpponentId] = useState('')
  const [stake, setStake] = useState('')
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState<string | null>(null)

  async function createChallenge() {
    setLoading(true)
    setMessage(null)
    try {
      await apiFetch('/challenge', {
        method: 'POST',
        token,
        body: {
          opponent_id: Number(opponentId),
          stake_amount: Number(stake),
        },
      })
      setMessage(t('challenge.challenge_sent'))
    } catch (e: any) {
      setMessage(e?.data?.message || 'Failed')
    } finally {
      setLoading(false)
    }
  }

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('challenge.challenge_title')}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-4">
        {message && <div className="text-sm">{message}</div>}
        <div className="grid md:grid-cols-2 gap-3">
          <label className="block text-sm">
            <span className="mb-1 block">Opponent ID</span>
            <input
              value={opponentId}
              onChange={(e) => setOpponentId(e.target.value)}
              className="w-full rounded-md bg-background border border-white/10 px-3 py-2"
            />
          </label>
          <label className="block text-sm">
            <span className="mb-1 block">Stake Amount (₦)</span>
            <input
              value={stake}
              onChange={(e) => setStake(e.target.value)}
              className="w-full rounded-md bg-background border border-white/10 px-3 py-2"
            />
          </label>
        </div>
        <button
          disabled={loading}
          onClick={() => void createChallenge()}
          className="bg-primary text-black px-4 py-2 rounded-md disabled:opacity-50"
        >
          {loading ? '...' : t('challenge.accept')}
        </button>
      </div>
    </section>
  )
}
