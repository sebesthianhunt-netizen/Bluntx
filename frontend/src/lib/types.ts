// Core API Types
export type WalletBalance = {
  cash_balance: number
  points_balance: number
  escrow_balance: number
  total_deposited: number
  total_withdrawn: number
}

export type Venue = {
  id: number
  name: string
  description?: string
  address?: string
  latitude?: number
  longitude?: number
  phone?: string
  is_active?: boolean
}

export type TableAvailability = {
  id: number
  table_number: string
  table_type: 'standard' | 'pro' | 'tournament'
  hourly_rate: number
  is_available: boolean
  available_slots: {
    start_time: string
    end_time: string
    price: number
  }[]
}

export type Challenge = {
  id: number
  challenger_id: number
  opponent_id: number
  stake_amount: number
  insurance_amount?: number
  total_escrow: number
  status: 'pending' | 'accepted' | 'declined' | 'expired' | 'in_progress' | 'completed' | 'disputed'
  expires_at?: string
  created_at?: string
}

export type Tournament = {
  id: number
  name: string
  description?: string
  venue_id: number
  tournament_type: 'single_elimination' | 'double_elimination' | 'round_robin' | 'swiss'
  entry_fee: number
  prize_pool: number
  max_participants: number
  current_participants?: number
  status: 'upcoming' | 'registration_open' | 'registration_closed' | 'in_progress' | 'completed'
}

export type Crew = {
  id: number
  name: string
  description?: string
  logo_url?: string | null
  banner_url?: string | null
  leader_id: number
  member_limit: number
  current_members?: number
  total_xp?: number
  is_public?: boolean
}

export type FeedPost = {
  id: number
  user_id: number
  type: 'text' | 'image' | 'video'
  content_url?: string
  caption?: string
  created_at?: string
}
