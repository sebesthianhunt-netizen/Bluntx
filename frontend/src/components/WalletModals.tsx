import { useState } from 'react'
import { useFundWallet, useWithdraw, useTransfer, usePollTx } from '@/lib/hooks'
import { toast } from '@/store/toast'

export function FundModal({ onClose }: { onClose: () => void }) {
  const [amount, setAmount] = useState('')
  const [provider, setProvider] = useState<'paystack' | 'flutterwave' | 'monnify'>('paystack')
  const fund = useFundWallet()
  const [ref, setRef] = useState<string | null>(null)
  const { data: tx } = usePollTx(ref)

  async function submit() {
    try {
      const r = await fund.mutateAsync({ amount: Number(amount), payment_provider: provider })
      const url = (r as any)?.data?.payment_url
      const refVal = (r as any)?.data?.reference
      if (url) window.open(url, '_blank')
      if (refVal) setRef(refVal)
      toast.success('Payment initiated')
      onClose()
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to fund')
    }
  }

  return (
    <Modal title="Fund Wallet" onClose={onClose}>
      <div className="space-y-3">
        {tx?.status && <div className="text-sm text-text-muted">Status: {tx.status}</div>}
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Amount" value={amount} onChange={(e) => setAmount(e.target.value)} />
        <select className="w-full bg-background border border-white/10 rounded px-3 py-2" value={provider} onChange={(e) => setProvider(e.target.value as any)}>
          <option value="paystack">Paystack</option>
          <option value="flutterwave">Flutterwave</option>
          <option value="monnify">Monnify</option>
        </select>
        <button onClick={() => void submit()} className="bg-primary text-black px-4 py-2 rounded-md">Continue</button>
      </div>
    </Modal>
  )
}

export function WithdrawModal({ onClose }: { onClose: () => void }) {
  const [amount, setAmount] = useState('')
  const [acct, setAcct] = useState('')
  const [bank, setBank] = useState('')
  const [provider, setProvider] = useState<'paystack' | 'flutterwave' | 'monnify'>('paystack')
  const wd = useWithdraw()

  async function submit() {
    try {
      await wd.mutateAsync({ amount: Number(amount), payment_provider: provider, destination: { type: 'bank_account', account_number: acct, bank_code: bank } })
      toast.success('Withdrawal submitted')
      onClose()
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to withdraw')
    }
  }

  return (
    <Modal title="Withdraw" onClose={onClose}>
      <div className="space-y-3">
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Amount" value={amount} onChange={(e) => setAmount(e.target.value)} />
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Account Number" value={acct} onChange={(e) => setAcct(e.target.value)} />
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Bank Code" value={bank} onChange={(e) => setBank(e.target.value)} />
        <select className="w-full bg-background border border-white/10 rounded px-3 py-2" value={provider} onChange={(e) => setProvider(e.target.value as any)}>
          <option value="paystack">Paystack</option>
          <option value="flutterwave">Flutterwave</option>
          <option value="monnify">Monnify</option>
        </select>
        <button onClick={() => void submit()} className="bg-primary text-black px-4 py-2 rounded-md">Submit</button>
      </div>
    </Modal>
  )
}

export function TransferModal({ onClose }: { onClose: () => void }) {
  const [recipient, setRecipient] = useState('')
  const [amount, setAmount] = useState('')
  const [note, setNote] = useState('')
  const tr = useTransfer()

  async function submit() {
    try {
      await tr.mutateAsync({ recipient_id: Number(recipient), amount: Number(amount), note })
      toast.success('Transfer sent')
      onClose()
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to transfer')
    }
  }

  return (
    <Modal title="Transfer" onClose={onClose}>
      <div className="space-y-3">
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Recipient ID" value={recipient} onChange={(e) => setRecipient(e.target.value)} />
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Amount" value={amount} onChange={(e) => setAmount(e.target.value)} />
        <input className="w-full bg-background border border-white/10 rounded px-3 py-2" placeholder="Note (optional)" value={note} onChange={(e) => setNote(e.target.value)} />
        <button onClick={() => void submit()} className="bg-primary text-black px-4 py-2 rounded-md">Send</button>
      </div>
    </Modal>
  )
}

function Modal({ title, onClose, children }: { title: string; onClose: () => void; children: any }) {
  return (
    <div className="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
      <div className="bg-card border border-white/10 rounded-xl w-full max-w-md p-5">
        <div className="flex items-center justify-between mb-3">
          <div className="font-bold">{title}</div>
          <button onClick={onClose} className="text-text-muted hover:text-primary">✕</button>
        </div>
        {children}
      </div>
    </div>
  )
}
