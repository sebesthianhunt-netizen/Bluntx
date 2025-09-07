import { create } from 'zustand'

export type ToastItem = {
  id: string
  type: 'success' | 'error' | 'info'
  message: string
}

type ToastState = {
  toasts: ToastItem[]
  addToast: (t: Omit<ToastItem, 'id'>) => void
  removeToast: (id: string) => void
}

export const useToastStore = create<ToastState>((set, get) => ({
  toasts: [],
  addToast: (t) => {
    const id = `${Date.now()}-${Math.random().toString(36).slice(2)}`
    set({ toasts: [...get().toasts, { ...t, id }] })
    setTimeout(() => get().removeToast(id), 3500)
  },
  removeToast: (id) => set({ toasts: get().toasts.filter((x) => x.id !== id) }),
}))

export const toast = {
  success(message: string) {
    useToastStore.getState().addToast({ type: 'success', message })
  },
  error(message: string) {
    useToastStore.getState().addToast({ type: 'error', message })
  },
  info(message: string) {
    useToastStore.getState().addToast({ type: 'info', message })
  },
}
