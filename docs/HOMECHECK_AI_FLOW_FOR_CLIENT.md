# HomeCheck AI Analysis – When Comments & Ratings Appear (Client Brief)

## When will the AI comment and rating be displayed?

**AI comments and ratings are displayed immediately after the AI process finishes.** As soon as the admin/agent clicks **“Process AI Analysis”** and the system completes the run (usually within a few seconds to a minute), the page refreshes and all AI ratings and comments are visible in the places described below. No extra step or wait is required.

---

## Whole scenario (end-to-end)

### 1. HomeCheck is completed (images uploaded)

- Admin or agent completes the HomeCheck: they upload room images (and optionally moisture readings) for the property.
- This is done from **Edit HomeCheck** (e.g. **Rooms** tab): add rooms, upload images per room, then complete/save the HomeCheck.
- At this point there are **no** AI comments or ratings yet—only the photos (and any manual moisture readings).

### 2. “Process AI Analysis” is run

- Admin/agent goes to the **HomeCheck detail page** (e.g. `http://localhost:8000/admin/homechecks/6`).
- If the HomeCheck has **at least one image** and **no AI report has been generated yet**, a green button is shown: **“Process AI Analysis”**.
- User clicks **“Process AI Analysis”** and confirms in the popup.
- The system then:
  - Sends the property and room/image data to the AI (OpenAI if configured, otherwise an internal fallback).
  - Gets back **per-room** ratings and comments (and optionally moisture).
  - Saves that analysis **to every image** in each room (so each image gets the same rating/comment as its room).
  - Generates the full AI report document and marks the HomeCheck as having a report.
- When this finishes, the user is **redirected back to the same HomeCheck page** with a success message.

### 3. Where AI comments and ratings appear (after the process)

As soon as the redirect happens, the following are visible:

| Where | What is shown |
|--------|----------------|
| **AI Report & Analysis** (card near the top) | One block per room: **AI rating** (e.g. 9/10), **AI comment** (full text). Shown only when an AI report has been generated. |
| **Each room section** | At the top of the room: **AI Rating** and **AI Analysis** (comment) for that room. |
| **Every image card** (in the gallery under each room) | **AI Rating** (e.g. 8/10 or “—”) and **AI Comment** (text or “—”) for that image. |
| **Image modal** (when you click an image) | That image’s **AI Rating** and **AI Comment** (and moisture if present). |
| **HomeChecks list** (`/admin/homechecks`) | An **“AI Report”** column: “✓ Report” (with link) when a report exists, “—” when not. |

So: **AI comment and rating are shown everywhere above as soon as the AI process has completed**—no second action or delay.

### 4. AI connection (real AI vs fallback)

- If **OpenAI** is configured in the app (API key + Assistant ID in environment), the analysis uses **real AI** and the **“✓ AI connected”** badge is shown on the HomeCheck list and detail pages.
- If not configured, the system uses an **internal fallback** (simulated analysis) and shows **“○ AI fallback”**. Comments and ratings still appear in the same places; they are just not from the live AI service.

### 5. One-line summary for your client

**“AI comments and ratings appear as soon as you run ‘Process AI Analysis’ on a completed HomeCheck: they show up on the same page (in the AI Report card, in each room, and on every image) right after the process finishes.”**
