import { Outlet } from 'react-router-dom'
import Header from '@/components/Header'
import BottomNav from '@/components/BottomNav'

export default function Layout() {
  return (
    <div className="min-h-screen">
      <Header />
      <div className="mx-auto max-w-6xl px-4 py-4 pb-20">
        <Outlet />
      </div>
      <BottomNav />
    </div>
  )
}
