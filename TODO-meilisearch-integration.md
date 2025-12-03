# Meilisearch Integration TODO

## Completed Tasks
- [x] Add Laravel Scout provider to bootstrap/providers.php
- [x] Add Searchable trait to User model
- [x] Add Searchable trait to Stock model
- [x] Add Searchable trait to ProductType model
- [x] Add Searchable trait to Farm model
- [x] Add toSearchableArray method to User model
- [x] Add toSearchableArray method to Stock model
- [x] Add toSearchableArray method to ProductType model
- [x] Create API route for users search
- [x] Create API route for stocks search
- [x] Create API route for product-types search
- [x] Create API route for farms search

## Pending Tasks
- [ ] Add toSearchableArray method to Farm model
- [ ] Test the search APIs
- [ ] Index models in Meilisearch (run scout:index commands)
- [ ] Add more models if needed (e.g., Role, Bank, etc.)
- [ ] Update views to use the new search APIs if necessary

## Notes
- Meilisearch is configured in docker-compose.yml and Scout driver is set to meilisearch
- The search-input component is generic and can be used with different API routes
- Routes are in routes/api_milisearch.php
