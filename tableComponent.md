# COMPONENT SPEC: DATA TABLES

## 1. Table Container Card
- Background: #FFFFFF
- Corner Radius: 16px
- Border: 1px solid #E2E8F0
- Shadow: Soft Depth Shadow (from tokens)
- Padding: Seamless integration with clean margins.

## 2. Table Header (thead)
- Background: #F8FAFC or a very soft blue (#F0F5FF)
- Text Color: #0A1C40 (Deep navy, uppercase, semibold, size: 12px/14px)
- Padding: 16px inside `<th>`
- Border Bottom: 2px solid #E2E8F0

## 3. Table Body Rows (tbody tr)
- Background: #FFFFFF
- Text Color: #334155 (Dark gray for body text)
- Padding: 16px inside `<td>`
- Border Bottom: 1px solid #F1F5F9 (Clean, borderless-vibe between rows)
- Hover State: Light background shift to #F8FAFC on row hover for better tracking.

## 4. Status Badges inside Table (e.g., "Aktif", "Selesai", "Pending")
- Corner Radius: 8px (Pill shape)
- Padding: 6px 12px
- Colors:
  * Approved/Aktif: Background #EFF6FF, Text #1E5EF3 (Using primary theme)
  * Pending/Proses: Background #FEF9C3, Text #A16207 (Using accent yellow theme)
  * Rejected/Batal: Background #FEE2E2, Text #DC2626
