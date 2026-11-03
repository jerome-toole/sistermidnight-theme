# Events module

- `PostType` / `LocationTaxonomy` register the `event` post type and `location` taxonomy.
- `Event` wraps an event post and its ACF fields; `EventRepository` queries events by start date.
- `EventDetailsBlock` pins the `theme/event-details` block (components/event-details) to the top of every event.
  Its ACF fields save to post meta (`usePostMeta`), so `start`, `venue_name` etc. are ordinary meta. Outside a
  block (seeds, scripts) write them with field keys, e.g. `update_field('field_event_start', …)`: the field group's
  location is the block, so lookups by name don't find it.
- `Feed` writes the calendar feed to `wp-content/uploads/events/feed.json`.

## Events feed

Rebuilt once per request (at shutdown) after `save_post_event`, trash/untrash/delete of an event,
or a term change on an event, and daily by the `sm_events_feed_regenerate` cron as a safety net.
It holds all future events plus the past 2 calendar months (`Feed::PAST_MONTHS`). Older events stay published but leave the calendar:

```json
{ "generated": 1760000000, "timezone": "Europe/London", "events": [
  { "id": 1, "title": "", "url": "", "start": "2026-04-01T19:00:00+01:00", "end_time": "2026-04-01T22:00:00+01:00",
    "end_label": "22:00", "venue": "", "presenter": "", "excerpt": "", "poster": { "src": "", "srcset": "", "alt": "" } }
] }
```

`end_time` and `poster` are `null` when not set. `Feed::url()` returns the file URL with `?v={generated}`.

`excerpt` is the manual excerpt, or the first 30 words of the description.

Regenerate manually:

```sh
wp --path=../../../ eval 'var_dump(\Theme\Modules\Events\Feed::regenerate());'
```
