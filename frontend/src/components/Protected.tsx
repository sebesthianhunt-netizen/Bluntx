import { Navigate, Outlet, useLocation } from 'react-router-dom'
import { useSessionStore } from '@/store/session'

export default function Protected() {
  const token = useSessionStore((s) => s.accessToken)
  const location = useLocation()
  if (!token) {
    return <Navigate to="/auth/login" replace state={{ from: location.pathname }} />
  }
  return <Outlet />
}
