import { env } from '@/lib/env'

export type HttpMethod = 'GET' | 'POST' | 'PATCH' | 'PUT' | 'DELETE'

export async function apiFetch<T>(
  path: string,
  options: {
    method?: HttpMethod
    body?: unknown
    token?: string | null
    headers?: Record<string, string>
    signal?: AbortSignal
  } = {}
): Promise<T> {
  const { method = 'GET', body, token, headers = {}, signal } = options
  const res = await fetch(`${env.API_BASE_URL}${path}`, {
    method,
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...headers,
    },
    body: body ? JSON.stringify(body) : undefined,
    signal,
    credentials: 'include',
  })

  const text = await res.text()
  const data = text ? (JSON.parse(text) as T) : ({} as T)

  if (!res.ok) {
    if (res.status === 401) {
      try { localStorage.removeItem('blvkdot_token') } catch {}
      if (typeof window !== 'undefined') {
        window.location.assign('/auth/login')
      }
    }
    throw { status: res.status, data }
  }
  return data
}
