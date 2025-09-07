import { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { useNavigate } from 'react-router-dom'

export default function LoginPage() {
  const { t } = useTranslation()
  const [phone, setPhone] = useState('')
  const [otp, setOtp] = useState('')
  const [step, setStep] = useState<'phone' | 'otp'>('phone')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const setAccessToken = useSessionStore((s) => s.setAccessToken)
  const navigate = useNavigate()

  async function requestOtp() {
    setLoading(true)
    setError(null)
    try {
      await apiFetch<unknown>('/auth/login', { method: 'POST', body: { phone } })
      setStep('otp')
    } catch (e: any) {
      setError(e?.data?.message || 'Failed')
    } finally {
      setLoading(false)
    }
  }

  async function verifyOtp() {
    setLoading(true)
    setError(null)
    try {
      const resp = await apiFetch<{ data: { access_token: string } }>('/auth/otp/verify', {
        method: 'POST',
        body: { phone, code: otp },
      })
      setAccessToken(resp.data.access_token)
      navigate('/', { replace: true })
    } catch (e: any) {
      setError(e?.data?.message || t('auth.otp_error'))
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center p-4">
      <div className="bg-card w-full max-w-md rounded-xl p-6 border border-white/5">
        <h1 className="text-2xl font-bold mb-1">{t('auth.onboarding_headline')}</h1>
        <p className="text-text-muted mb-6">{t('auth.onboarding_sub')}</p>
        {error && (
          <div className="mb-3 text-sm text-danger">{error}</div>
        )}
        {step === 'phone' ? (
          <form className="space-y-4" onSubmit={(e) => { e.preventDefault(); void requestOtp() }}>
            <label className="block text-sm">
              <span className="mb-1 block">Phone</span>
              <input
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                placeholder="+2348012345678"
                className="w-full rounded-md bg-background border border-white/10 px-3 py-2 outline-none focus:border-primary"
              />
            </label>
            <button
              disabled={loading}
              type="submit"
              className="w-full bg-primary text-black font-semibold px-4 py-2 rounded-md hover:opacity-90 disabled:opacity-50"
            >
              {loading ? '...' : t('auth.login_cta')}
            </button>
          </form>
        ) : (
          <form className="space-y-4" onSubmit={(e) => { e.preventDefault(); void verifyOtp() }}>
            <label className="block text-sm">
              <span className="mb-1 block">OTP</span>
              <input
                value={otp}
                onChange={(e) => setOtp(e.target.value)}
                placeholder="123456"
                className="w-full rounded-md bg-background border border-white/10 px-3 py-2 outline-none focus:border-primary"
              />
            </label>
            <button
              disabled={loading}
              type="submit"
              className="w-full bg-primary text-black font-semibold px-4 py-2 rounded-md hover:opacity-90 disabled:opacity-50"
            >
              {loading ? '...' : t('auth.signup_cta')}
            </button>
          </form>
        )}
      </div>
    </div>
  )
}
