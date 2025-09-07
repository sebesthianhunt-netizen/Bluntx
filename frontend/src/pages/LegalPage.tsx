import { useTranslation } from 'react-i18next'

export default function LegalPage() {
  const { t } = useTranslation()
  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">Legal</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-2 text-sm text-text-muted">
        <p>{t('legal.tos')}</p>
        <p>{t('legal.privacy')}</p>
        <p>{t('legal.age_gate')}</p>
      </div>
    </section>
  )
}
