import { create } from 'zustand'

type SettingsState = {
  dataSaver: boolean
  reducedMotion: boolean
  textScale: number // 1.0 default
  setDataSaver: (v: boolean) => void
  setReducedMotion: (v: boolean) => void
  setTextScale: (v: number) => void
}

export const useSettingsStore = create<SettingsState>((set) => ({
  dataSaver: false,
  reducedMotion: false,
  textScale: 1,
  setDataSaver: (v) => set({ dataSaver: v }),
  setReducedMotion: (v) => set({ reducedMotion: v }),
  setTextScale: (v) => set({ textScale: v }),
}))
