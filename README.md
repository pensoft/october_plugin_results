# Pensoft.Results

Project results shown as an interactive timeline — the "What CONCERTO Achieved"
graphic from CON-86.

The timeline is a horizontal axis carrying the project phases as large circles,
with the individual results pinned above and below it. A row of audience toggles
filters which results are visible, and clicking a result opens a pop-up with its
description and three collapsible sections.

## Backend

**Results → Results** — the result nodes.

| Field | Purpose |
| --- | --- |
| Title / Month label | Caption under the icon, e.g. `M18-M48` |
| Published | Unpublished results are hidden from the timeline |
| Phase | The large circle on the axis the result belongs to |
| Side of the timeline | `above` or `below` the axis |
| Horizontal position (%) | Distance from the left edge, `0`–`100` |
| Icon | SVG or PNG shown inside the circle |
| Icon CSS class | Fallback when no icon file is uploaded, e.g. an icomoon class |
| Relevant for | Audiences whose toggle reveals this result |
| Description | Intro paragraph of the pop-up |
| Who is it relevant for / How can it be used / Available materials | The three collapsible sections of the pop-up |

**Results → Timeline phases** — the four circles on the axis, each with its own
horizontal position.

**Results → Audiences** — the audience toggles. Unticking *Visible* removes the
toggle without touching the results assigned to it.

A result with no audience selected only appears under the **All** toggle.

## Component

`results_timeline`

```ini
[results_timeline]
title = "What CONCERTO Achieved"
audienceLabel = "Who is it relevant for?"
showAllToggle = 1
iconSize = 40
```

```twig
{% component "results_timeline" %}
```

The partial loads its script through `{% put scripts %}`, so the layout needs a
`{% scripts %}` tag — the CONCERTO default layout already has one.

**Markup and styling live with the theme**, not the plugin:

| | Path in `themes/pensoft-concerto/` |
| --- | --- |
| Markup | `partials/results_timeline/default.htm` |
| Styles | `assets/less/components/results-timeline.less` (imported from `theme.less`) |
| Behaviour | `assets/js/results-timeline.js` (pulled in by the partial) |

October resolves the markup override by the component's **alias**, so the
partial directory has to match the alias the page declares it under. Renaming
`[results_timeline]` to anything else means renaming that directory too,
otherwise the component renders nothing.

The plugin ships data and the component class only — a theme rendering this
component has to supply its own partial, styles and script.

Positioning is absolute above ~992px and collapses to a plain stack below that.
Each node's distance from the axis comes through as a `--rt-offset` custom
property on the node, so the stylesheet can still flatten it on narrow screens.

## Install

Migrations run automatically the first time the plugin is loaded, or explicitly:

```
php artisan october:up
```

`SeedResultsData` seeds the phases, audiences and the ten result nodes from the
approved design — titles, month labels, positions and audience assignments. The
body copy of each pop-up is left empty on purpose and is filled in from the
backend.
