import { create } from 'zustand'

const TOKEN_KEY = 'blvkdot_token'

function getInitialToken(): string | null {
  try { return localStorage.getItem(TOKEN_KEY) } catch { return null }
}

type SessionState = {
  accessToken: string | null
  setAccessToken: (token: string | null) => void
  logout: () => void
}

export const useSessionStore = create<SessionState>((set) => ({
  accessToken: getInitialToken(),
  setAccessToken: (token) => {
    try {
      if (token) localStorage.setItem(TOKEN_KEY, token)
      else localStorage.removeItem(TOKEN_KEY)
    } catch {}
    set({ accessToken: token })
  },
  logout: () => {
    try { localStorage.removeItem(TOKEN_KEY) } catch {}
    set({ accessToken: null })
  }
}))
