import { useTranslation } from 'react-i18next'

export default function LeaderboardPage() {
  const { t } = useTranslation()
  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('home.view_leaderboard')}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6">
        <p className="text-text-muted">{t('leaderboard') || 'Leaderboard coming soon…'}</p>
      </div>
    </section>
  )
}
