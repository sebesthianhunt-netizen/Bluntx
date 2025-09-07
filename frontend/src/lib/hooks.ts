import { useQuery, useMutation } from '@tanstack/react-query'
import { apiFetch } from '@/lib/apiClient'
import { WalletBalance, Venue, TableAvailability, Challenge, Tournament } from '@/lib/types'
import { useSessionStore } from '@/store/session'

export function useAuthToken() {
  const token = useSessionStore((s) => s.accessToken)
  return token
}

export function useWallet() {
  const token = useAuthToken()
  return useQuery({
    queryKey: ['wallet'],
    queryFn: () => apiFetch<WalletBalance>('/wallet', { token }),
    enabled: !!token,
  })
}

export function useWalletHistory(page = 1, perPage = 20) {
  const token = useAuthToken()
  return useQuery({
    queryKey: ['wallet-history', page, perPage],
    queryFn: () => apiFetch<{ data: any[]; pagination?: any }>(`/wallet/history?page=${page}&per_page=${perPage}`, { token }),
    enabled: !!token,
  })
}

export function useFundWallet() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { amount: number; payment_provider: 'paystack' | 'flutterwave' | 'monnify'; currency?: string }) =>
      apiFetch<{ data: { payment_url: string; reference: string } }>(`/wallet/fund`, { method: 'POST', body: payload, token }),
  })
}

export function useWithdraw() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { amount: number; payment_provider: 'paystack' | 'flutterwave' | 'monnify'; currency?: string; destination: any }) =>
      apiFetch(`/wallet/withdraw`, { method: 'POST', body: payload, token }),
  })
}

export function usePollTx(reference: string | null) {
  const token = useAuthToken()
  return useQuery({
    queryKey: ['wallet-tx', reference],
    queryFn: () => apiFetch<{ status: string }>(`/wallet/tx/${reference}`, { token }),
    enabled: !!token && !!reference,
    refetchInterval: 2000,
  })
}

export function useTransfer() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { recipient_id: number; amount: number; note?: string }) =>
      apiFetch(`/wallet/transfer`, { method: 'POST', body: payload, token }),
  })
}

export function useVenues(params?: { lat?: number; lng?: number; radius?: number }) {
  const query = new URLSearchParams()
  if (params?.lat) query.set('lat', String(params.lat))
  if (params?.lng) query.set('lng', String(params.lng))
  if (params?.radius) query.set('radius', String(params.radius))
  return useQuery({
    queryKey: ['venues', params],
    queryFn: () => apiFetch<{ data: any[] }>(`/venues${query.toString() ? `?${query.toString()}` : ''}`),
  })
}

export function useTables(venueId?: number, date?: string) {
  return useQuery({
    queryKey: ['tables', venueId, date],
    queryFn: () => apiFetch<{ data: TableAvailability[] }>(`/venues/${venueId}/tables?date=${date}`),
    enabled: !!venueId && !!date,
  })
}

export function useBook() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { table_id: number; start_time: string; end_time: string; notes?: string }) =>
      apiFetch('/booking', { method: 'POST', body: payload, token }),
  })
}

export function useChallengeHistory(page = 1, perPage = 20) {
  const token = useAuthToken()
  return useQuery({
    queryKey: ['challenge-history', page, perPage],
    queryFn: () => apiFetch<{ data: Challenge[] }>(`/challenge/history?page=${page}&per_page=${perPage}`, { token }),
    enabled: !!token,
  })
}

export function useTournaments(params?: { status?: string; venue_id?: number }) {
  const query = new URLSearchParams()
  if (params?.status) query.set('status', params.status)
  if (params?.venue_id) query.set('venue_id', String(params.venue_id))
  return useQuery({
    queryKey: ['tournaments', params],
    queryFn: () => apiFetch<{ data: Tournament[] }>(`/tournaments${query.toString() ? `?${query.toString()}` : ''}`),
  })
}

export function useTournamentBracket(tournamentId?: number) {
  return useQuery({
    queryKey: ['tournament-bracket', tournamentId],
    queryFn: () => apiFetch<any>(`/tournaments/${tournamentId}/bracket`),
    enabled: !!tournamentId,
  })
}

export function useCrews() {
  const token = useAuthToken()
  return useQuery({
    queryKey: ['crews'],
    queryFn: () => apiFetch<{ data: any[] }>(`/crew`, { token }),
    enabled: !!token,
  })
}

export function useFeed() {
  return useQuery({
    queryKey: ['feed'],
    queryFn: () => apiFetch<{ data: any[] }>(`/feed`),
  })
}

// Admin
export function useCreateVenue() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { name: string; address?: string; is_active?: boolean }) =>
      apiFetch(`/admin/venues`, { method: 'POST', body: payload, token }),
  })
}
export function useUpdateVenue() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (args: { id: number; data: { name?: string; address?: string; is_active?: boolean } }) =>
      apiFetch(`/admin/venues/${args.id}`, { method: 'PATCH', body: args.data, token }),
  })
}
export function useDeleteVenue() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (id: number) => apiFetch(`/admin/venues/${id}`, { method: 'DELETE', token }),
  })
}
export function useCreateTable() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (args: { venueId: number; data: { table_number: number; table_type: string; hourly_rate: number } }) =>
      apiFetch(`/admin/venues/${args.venueId}/tables`, { method: 'POST', body: args.data, token }),
  })
}
export function useUpdateTable() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (args: { id: number; data: { table_number?: number; table_type?: string; hourly_rate?: number } }) =>
      apiFetch(`/admin/tables/${args.id}`, { method: 'PATCH', body: args.data, token }),
  })
}
export function useDeleteTable() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (id: number) => apiFetch(`/admin/tables/${id}`, { method: 'DELETE', token }),
  })
}

// Attendant
export function useCheckin() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (bookingId: number) => apiFetch(`/booking/${bookingId}/checkin`, { method: 'POST', token }),
  })
}
export function useCheckout() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (bookingId: number) => apiFetch(`/booking/${bookingId}/checkout`, { method: 'POST', token }),
  })
}

// Tournament admin
export function useTournamentSeed() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (args: { id: number; user_ids: number[] }) => apiFetch(`/tournaments/${args.id}/seed`, { method: 'POST', body: { user_ids: args.user_ids }, token }),
  })
}
export function useTournamentResult() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (args: { id: number; match_id: number; winner_id: number }) => apiFetch(`/tournaments/${args.id}/result`, { method: 'POST', body: { match_id: args.match_id, winner_id: args.winner_id }, token }),
  })
}

// Moderation & device
export function useFlagContent() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { content_type: string; content_id: number; reason?: string }) => apiFetch(`/flags`, { method: 'POST', body: payload, token }),
  })
}
export function useRegisterDevice() {
  const token = useAuthToken()
  return useMutation({
    mutationFn: (payload: { device_id: string; push_token: string; platform: 'ios' | 'android' | 'web' }) => apiFetch(`/devices/register`, { method: 'POST', body: payload, token }),
  })
}
