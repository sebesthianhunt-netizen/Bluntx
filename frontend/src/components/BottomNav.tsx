import { Link, useLocation } from 'react-router-dom'
import clsx from 'clsx'

export default function BottomNav() {
  const location = useLocation()
  const items = [
    { to: '/', label: '🏠' },
    { to: '/booking', label: '🎱' },
    { to: '/leaderboard', label: '👑' },
    { to: '/wallet', label: '💳' },
    { to: '/settings', label: '🙍‍♂️' },
  ]
  return (
    <nav className="sm:hidden fixed bottom-0 left-0 right-0 bg-card border-t border-white/5">
      <ul className="grid grid-cols-5 text-xl">
        {items.map((it) => (
          <li key={it.to}>
            <Link
              to={it.to}
              className={clsx(
                'block text-center py-3',
                location.pathname === it.to ? 'text-primary' : 'text-text-muted'
              )}
            >
              {it.label}
            </Link>
          </li>
        ))}
      </ul>
    </nav>
  )
}
