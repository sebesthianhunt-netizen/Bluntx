import { Routes, Route, Navigate } from 'react-router-dom'
import Layout from '@/components/Layout'
import Protected from '@/components/Protected'
import HomePage from '@/pages/HomePage'
import LoginPage from '@/pages/LoginPage'
import BookingPage from '@/pages/BookingPage'
import BookingConfirmPage from '@/pages/BookingConfirmPage'
import BookingQRPage from '@/pages/BookingQRPage'
import WalletPage from '@/pages/WalletPage'
import WalletHistoryPage from '@/pages/WalletHistoryPage'
import ChallengePage from '@/pages/ChallengePage'
import CrewPage from '@/pages/CrewPage'
import TournamentsPage from '@/pages/TournamentsPage'
import TournamentDetailsPage from '@/pages/TournamentDetailsPage'
import LeaderboardPage from '@/pages/LeaderboardPage'
import FeedPage from '@/pages/FeedPage'
import SettingsPage from '@/pages/SettingsPage'
import SupportPage from '@/pages/SupportPage'
import LegalPage from '@/pages/LegalPage'
import ChallengeHistoryPage from '@/pages/ChallengeHistoryPage'
import ProfilePage from '@/pages/ProfilePage'
import MerchPage from '@/pages/MerchPage'
import OnboardingPage from '@/pages/OnboardingPage'
import AdminPage from '@/pages/AdminPage'
import AgentPage from '@/pages/AgentPage'
import AttendantPage from '@/pages/AttendantPage'

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/auth/login" element={<LoginPage />} />
      <Route element={<Layout />}>
        <Route index element={<HomePage />} />
        <Route element={<Protected />}>
          <Route path="/booking" element={<BookingPage />} />
          <Route path="/booking/confirm" element={<BookingConfirmPage />} />
          <Route path="/booking/qr" element={<BookingQRPage />} />
          <Route path="/wallet" element={<WalletPage />} />
          <Route path="/wallet/history" element={<WalletHistoryPage />} />
          <Route path="/challenge" element={<ChallengePage />} />
          <Route path="/challenge/history" element={<ChallengeHistoryPage />} />
          <Route path="/crew" element={<CrewPage />} />
          <Route path="/profile" element={<ProfilePage />} />
          <Route path="/onboarding" element={<OnboardingPage />} />
          <Route path="/admin" element={<AdminPage />} />
          <Route path="/agent" element={<AgentPage />} />
          <Route path="/attendant" element={<AttendantPage />} />
        </Route>
        <Route path="/merch" element={<MerchPage />} />
        <Route path="/tournaments" element={<TournamentsPage />} />
        <Route path="/tournaments/:id" element={<TournamentDetailsPage />} />
        <Route path="/leaderboard" element={<LeaderboardPage />} />
        <Route path="/feed" element={<FeedPage />} />
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="/support" element={<SupportPage />} />
        <Route path="/legal" element={<LegalPage />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Route>
    </Routes>
  )
}
