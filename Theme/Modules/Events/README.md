# Events module

- `PostType` / `LocationTaxonomy` register the `event` post type and `location` taxonomy.
- `Event` wraps an event post and its ACF fields; `EventRepository` queries events by start date.
- `Feed` writes the calendar feed to `wp-content/uploads/events/feed.json`.

## Events feed

Rebuilt once per request (at shutdown) after `save_post_event`, trash/untrash/delete of an event,
or a term change on an event, and daily by the `sm_events_feed_regenerate` cron as a safety net.
It holds all future events plus the past 3 months:

```json
{ "generated": 1760000000, "timezone": "Europe/London", "events": [
  { "id": 1, "title": "", "url": "", "start": "2026-04-01T19:00:00+01:00", "end_time": "2026-04-01T22:00:00+01:00",
    "end_label": "22:00", "venue": "", "presenter": "", "poster": { "src": "", "srcset": "", "alt": "" } }
] }
```

`end_time` and `poster` are `null` when not set. `Feed::url()` returns the file URL with `?v={generated}`.

Older months: `GET /wp-json/sm/v1/events?before=YYYY-MM` returns the same shape for the 3 months
before that month (exclusive), cached in a transient for 1 hour and invalidated on regeneration.

Regenerate manually:

```sh
wp --path=../../../ eval 'var_dump(\Theme\Modules\Events\Feed::regenerate());'
```
