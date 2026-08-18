import * as React from "react"
import { cva } from "class-variance-authority";
import { Slot } from "radix-ui"

import { cn } from "@/lib/utils"

const buttonVariants = cva(
  "group/button inline-flex shrink-0 cursor-pointer items-center justify-center rounded-lg border border-transparent bg-clip-padding text-sm font-medium whitespace-nowrap transition-all outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:not-aria-[haspopup]:translate-y-px disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default: "bg-primary text-primary-foreground hover:bg-primary/80",
        outline:
          "border-border bg-background hover:bg-muted hover:text-foreground aria-expanded:bg-muted aria-expanded:text-foreground dark:border-input dark:bg-input/30 dark:hover:bg-input/50",
        secondary:
          "bg-secondary text-secondary-foreground hover:bg-[color-mix(in_oklch,var(--secondary),var(--foreground)_5%)] aria-expanded:bg-secondary aria-expanded:text-secondary-foreground",
        ghost:
          "hover:bg-muted hover:text-foreground aria-expanded:bg-muted aria-expanded:text-foreground dark:hover:bg-muted/50",
        destructive:
          "bg-destructive/10 text-destructive hover:bg-destructive/20 focus-visible:border-destructive/40 focus-visible:ring-destructive/20 dark:bg-destructive/20 dark:hover:bg-destructive/30 dark:focus-visible:ring-destructive/40",
        link: "text-primary underline-offset-4 hover:underline",
      },
      size: {
        default: "h-9 gap-1.5 px-2.5",
        xs: "h-7 gap-1 rounded-[min(var(--radius-md),10px)] px-2 text-xs in-data-[slot=button-group]:rounded-lg [&_svg:not([class*='size-'])]:size-3",
        sm: "h-8 gap-1 rounded-[min(var(--radius-md),12px)] px-2.5 text-[0.925rem] in-data-[slot=button-group]:rounded-lg [&_svg:not([class*='size-'])]:size-3.5",
        lg: "h-10 gap-1.5 px-2.5",
        icon: "size-9",
        "icon-xs":
          "size-7 rounded-[min(var(--radius-md),10px)] in-data-[slot=button-group]:rounded-lg [&_svg:not([class*='size-'])]:size-3",
        "icon-sm":
          "size-8 rounded-[min(var(--radius-md),12px)] in-data-[slot=button-group]:rounded-lg",
        "icon-lg": "size-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
)

function withIconSpacer(nodes) {
  const iconChildren = nodes.filter(
    (child) =>
      React.isValidElement(child) &&
      (child.props?.["data-icon"] === "inline-start" || child.props?.["data-icon"] === "inline-end")
  )

  if (iconChildren.length !== 1 || nodes.length <= 1) return null

  const iconChild = iconChildren[0]
  const spacer = React.cloneElement(iconChild, {
    key: "icon-spacer",
    "data-icon": undefined,
    "aria-hidden": "true",
    className: cn(iconChild.props.className, "invisible pointer-events-none"),
  })

  return iconChild.props["data-icon"] === "inline-start"
    ? [...nodes, spacer]
    : [spacer, ...nodes]
}

function Button({
  className,
  variant = "default",
  size = "default",
  asChild = false,
  children,
  ...props
}) {
  const Comp = asChild ? Slot.Root : "button"

  const childArray = React.Children.toArray(children)

  let content = children
  if (asChild && childArray.length === 1 && React.isValidElement(childArray[0])) {
    // asChild (Slot) renders icon+text as children of the single wrapped element
    // (e.g. <a>/<Link>), not as direct children of Button — look one level deeper.
    const onlyChild = childArray[0]
    const innerContent = withIconSpacer(React.Children.toArray(onlyChild.props.children))
    if (innerContent) {
      content = React.cloneElement(onlyChild, {}, innerContent)
    }
  } else {
    const topContent = withIconSpacer(childArray)
    if (topContent) content = topContent
  }

  return (
    <Comp
      data-slot="button"
      data-variant={variant}
      data-size={size}
      className={cn(buttonVariants({ variant, size, className }))}
      {...props}>
      {content}
    </Comp>
  );
}

export { Button, buttonVariants }
