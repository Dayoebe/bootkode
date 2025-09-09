{{ $campaign->subject }}

{{ strip_tags($textContent) }}

---

You received this email because you're subscribed to our newsletter.
Unsubscribe: {{ $unsubscribe_url }}

© {{ date('Y') }} {{ $campaign->from_name }}. All rights reserved.