import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { useSessionStore } from '@/store/session'

export default function Header() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const logout = useSessionStore((s) => s.logout)
  return (
    <header className="sticky top-0 z-10 bg-background/80 backdrop-blur border-b border-white/5">
      <div className="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
        <Link to="/" className="text-primary font-extrabold tracking-wide">
          {t('global.app_name')}
        </Link>
        <nav className="hidden sm:flex gap-4 text-sm items-center">
          <Link to="/booking" className="text-text-muted hover:text-primary">{t('home.book_table')}</Link>
          <Link to="/challenge" className="text-text-muted hover:text-primary">{t('challenge.challenge_title')}</Link>
          <Link to="/wallet" className="text-text-muted hover:text-primary">{t('home.wallet_balance')}</Link>
          <Link to="/crew" className="text-text-muted hover:text-primary">{t('crew.crew_home')}</Link>
          <Link to="/feed" className="text-text-muted hover:text-primary">{t('feed.clips_feed')}</Link>
          {!token ? (
            <Link to="/auth/login" className="text-text-muted hover:text-primary">{t('auth.login_cta')}</Link>
          ) : (
            <button onClick={logout} className="text-text-muted hover:text-primary">Logout</button>
          )}
        </nav>
      </div>
    </header>
  )
}
