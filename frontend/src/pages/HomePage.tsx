import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

export default function HomePage() {
  const { t } = useTranslation()
  return (
    <div className="min-h-screen p-4 sm:p-6 md:p-8">
      <header className="flex items-center justify-between mb-6">
        <div className="text-2xl font-bold tracking-wide">
          {t('global.app_name')}
        </div>
        <nav className="flex gap-4 text-sm">
          <Link to="/auth/login" className="text-text-muted hover:text-primary">
            {t('auth.login_cta')}
          </Link>
        </nav>
      </header>

      <main className="space-y-6">
        <section className="bg-card rounded-xl p-5 border border-white/5">
          <h1 className="text-3xl font-extrabold mb-2 text-primary">
            {t('marketing.main_headline')}
          </h1>
          <p className="text-text-muted mb-4">{t('marketing.sub_headline')}</p>
          <Link
            to="/booking"
            className="inline-block bg-primary text-black font-semibold px-4 py-2 rounded-md hover:opacity-90"
          >
            {t('cta.play_now')}
          </Link>
        </section>

        <section className="grid md:grid-cols-2 gap-4">
          <div className="bg-card rounded-xl p-5 border border-white/5">
            <h2 className="text-xl font-bold mb-2">{t('home.book_table')}</h2>
            <p className="text-text-muted">{t('booking.calendar_title')}</p>
          </div>
          <div className="bg-card rounded-xl p-5 border border-white/5">
            <h2 className="text-xl font-bold mb-2">{t('home.challenge_someone')}</h2>
            <p className="text-text-muted">{t('challenge.stake_prompt')}</p>
          </div>
        </section>
      </main>
    </div>
  )
}
