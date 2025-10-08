<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cat Art Gallery</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background-color: #f8f8f8;
    }
    h1 {
      text-align: center;
    }
    #gallery {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      grid-gap: 16px;
      margin-top: 20px;
    }
    .card {
      background: white;
      border: 1px solid #ddd;
      border-radius: 4px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .card img {
      width: 100%;
      object-fit: cover;
    }
    .card .info {
      padding: 8px;
      flex-grow: 1;
    }
    .card .info h3 {
      margin: 0 0 8px 0;
      font-size: 1.1em;
    }
    .card .info p {
      margin: 0;
      font-size: 0.9em;
      color: #555;
    }
  </style>
</head>
<body>
  <h1>Cat Art 🐱</h1>
  <div id="gallery">Loading…</div>

  <script>
    const galleryEl = document.getElementById('gallery');
    const apiUrl = 'https://api.artic.edu/api/v1/artworks/search?q=cats&limit=12&fields=id,title,image_id,artist_display,_score';

    async function fetchArtworks() {
      try {
        const resp = await fetch(apiUrl);
        if (!resp.ok) {
          throw new Error('Network error: ' + resp.status);
        }
        const data = await resp.json();
        const artworks = data.data;
        const config = data.config;  // config contains base_url and iiif_url etc.

        galleryEl.innerHTML = '';  // clear “Loading…”

        artworks.forEach(item => {
          const { id, title, image_id, artist_display, _score } = item;
          // If no image_id, skip
          if (!image_id) return;

          // Construct image URL via IIIF (per API docs)
          const iiifUrl = config.iiif_url;
          const imgUrl = `${iiifUrl}/${image_id}/full/400,/0/default.jpg`;

          const card = document.createElement('div');
          card.className = 'card';

          const img = document.createElement('img');
          img.src = imgUrl;
          img.alt = title;

          const info = document.createElement('div');
          info.className = 'info';
          const h3 = document.createElement('h3');
          h3.textContent = title;
          const p = document.createElement('p');
          p.textContent = artist_display || 'Unknown artist';

          // Added: ID and _score
          const meta = document.createElement('p');
          meta.textContent = `ID: ${id} | Score: ${_score}`;

          info.appendChild(h3);
          info.appendChild(p);
          info.appendChild(meta);

          card.appendChild(img);
          card.appendChild(info);

          galleryEl.appendChild(card);
        });

      } catch (err) {
        console.error('Error fetching artworks:', err);
        galleryEl.innerHTML = `<p>Failed to load artworks: ${err.message}</p>`;
      }
    }

    fetchArtworks();
  </script>
</body>
</html>
