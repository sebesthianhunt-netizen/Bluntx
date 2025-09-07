import { useToastStore } from '@/store/toast'

export default function ToastHost() {
  const toasts = useToastStore((s) => s.toasts)
  return (
    <div className="fixed bottom-20 right-4 space-y-2 z-50">
      {toasts.map((t) => (
        <div
          key={t.id}
          className={`rounded-md px-4 py-2 text-sm shadow border ${
            t.type === 'success'
              ? 'bg-green-900/80 border-green-500/30'
              : t.type === 'error'
              ? 'bg-red-900/80 border-red-500/30'
              : 'bg-card border-white/10'
          }`}
        >
          {t.message}
        </div>
      ))}
    </div>
  )
}
