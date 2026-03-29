import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Button } from "./Button.vue"

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
  {
    variants: {
      variant: {
        default:
          //"bg-primary text-primary-foreground hover:bg-primary/90",
          "bg-[#1a3a5a] text-white shadow-lg shadow-blue-900/20 hover:bg-[#122a42]",
        destructive:
          //"bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",
          "bg-[#d90000] text-white shadow-lg shadow-red-900/20 hover:bg-[#b30000]",
        outline:
          //"border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50",
          "border-2 border-neutral-200 bg-white hover:bg-neutral-50 hover:text-neutral-900 text-neutral-600",
        secondary:
          //"bg-secondary text-secondary-foreground hover:bg-secondary/80",
          "bg-neutral-100 text-neutral-900 hover:bg-neutral-200",
        ghost:
          //"hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50",
          "hover:bg-neutral-100 hover:text-neutral-900",
        link:
          //"text-primary underline-offset-4 hover:underline",
          "text-[#1a3a5a] underline-offset-4 hover:underline",
      },
      size: {
        "default":
        "h-11 px-6 py-2",
        //"h-9 px-4 py-2 has-[>svg]:px-3",
        "sm":
        "h-9 rounded-lg px-3 text-xs",
        //"h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5",
        "lg":
        "h-12 rounded-2xl px-8",
        //"h-10 rounded-md px-6 has-[>svg]:px-4",
        "icon": "size-9",
        "icon-sm": "size-8",
        "icon-lg": "size-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)
export type ButtonVariants = VariantProps<typeof buttonVariants>
