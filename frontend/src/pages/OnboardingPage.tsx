import { useState } from 'react'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { useTranslation } from 'react-i18next'

export default function OnboardingPage() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const [step, setStep] = useState<'nickname' | 'avatar' | 'done'>('nickname')
  const [nickname, setNickname] = useState('')
  const [avatar, setAvatar] = useState('')
  const [loading, setLoading] = useState(false)

  async function next() {
    setLoading(true)
    try {
      if (step === 'nickname') {
        await apiFetch('/user/profile', { method: 'PATCH', token, body: { nickname } })
        setStep('avatar')
      } else if (step === 'avatar') {
        await apiFetch('/user/profile', { method: 'PATCH', token, body: { avatar_url: avatar } })
        setStep('done')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <section className="min-h-[60vh] flex items-center justify-center p-4">
      <div className="bg-card border border-white/5 rounded-xl p-6 w-full max-w-md">
        {step === 'nickname' && (
          <div className="space-y-3">
            <h1 className="text-2xl font-bold">{t('auth.nickname_prompt')}</h1>
            <input value={nickname} onChange={(e) => setNickname(e.target.value)} className="w-full bg-background border border-white/10 rounded px-3 py-2" />
            <button onClick={() => void next()} disabled={loading} className="bg-primary text-black px-4 py-2 rounded-md w-full">{t('global.continue')}</button>
          </div>
        )}
        {step === 'avatar' && (
          <div className="space-y-3">
            <h1 className="text-2xl font-bold">{t('auth.avatar_prompt')}</h1>
            <input value={avatar} onChange={(e) => setAvatar(e.target.value)} className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="https://..." />
            <button onClick={() => void next()} disabled={loading} className="bg-primary text-black px-4 py-2 rounded-md w-full">{t('global.continue')}</button>
          </div>
        )}
        {step === 'done' && (
          <div className="space-y-3 text-center">
            <div className="text-2xl font-bold">{t('auth.calibration_done')}</div>
          </div>
        )}
      </div>
    </section>
  )
}
