export { default as Table } from './Table.vue'

import { h } from 'vue'
import { cn } from '@/lib/utils'

export const TableHeader = (props: any, { slots }: any) =>
  h('thead', { class: cn('[&_tr]:border-b', props.class) }, slots)

export const TableRow = (props: any, { slots }: any) =>
  h('tr', { class: cn('border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', props.class) }, slots)

export const TableHead = (props: any, { slots }: any) =>
  h('th', { class: cn('h-12 px-4 text-left align-middle font-medium text-muted-foreground', props.class) }, slots)

export const TableCell = (props: any, { slots }: any) =>
  h('td', { class: cn('p-4 align-middle', props.class) }, slots)

export const TableBody = (props: any, { slots }: any) =>
  h('tbody', { class: cn('[&_tr:last-child]:border-0', props.class) }, slots)
