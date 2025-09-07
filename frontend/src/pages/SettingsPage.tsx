import { useTranslation } from 'react-i18next'
import { useSettingsStore } from '@/store/settings'
import { setLanguage } from '@/i18n/setup'

export default function SettingsPage() {
  const { t } = useTranslation()
  const dataSaver = useSettingsStore((s) => s.dataSaver)
  const reducedMotion = useSettingsStore((s) => s.reducedMotion)
  const textScale = useSettingsStore((s) => s.textScale)
  const setDataSaver = useSettingsStore((s) => s.setDataSaver)
  const setReducedMotion = useSettingsStore((s) => s.setReducedMotion)
  const setTextScale = useSettingsStore((s) => s.setTextScale)

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-bold">Settings</h1>

      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-3">
        <div className="flex items-center justify-between">
          <div>
            <div className="font-semibold">{t('data_saver.activate')}</div>
            <div className="text-text-muted text-sm">{dataSaver ? t('data_saver.activated') : ''}</div>
          </div>
          <input type="checkbox" checked={dataSaver} onChange={(e) => setDataSaver(e.target.checked)} />
        </div>
        <div className="flex items-center justify-between">
          <div>
            <div className="font-semibold">Reduced Motion</div>
            <div className="text-text-muted text-sm">Minimize animations</div>
          </div>
          <input type="checkbox" checked={reducedMotion} onChange={(e) => setReducedMotion(e.target.checked)} />
        </div>
        <div>
          <div className="font-semibold mb-2">Text Size</div>
          <input type="range" min={0.8} max={1.4} step={0.1} value={textScale} onChange={(e) => setTextScale(Number(e.target.value))} />
        </div>
        <div>
          <div className="font-semibold mb-2">Language</div>
          <div className="flex gap-2">
            <button onClick={() => setLanguage('en')} className="border border-white/10 rounded px-3 py-1">English</button>
            <button onClick={() => setLanguage('pg')} className="border border-white/10 rounded px-3 py-1">Pidgin</button>
          </div>
        </div>
      </div>
    </section>
  )
}
