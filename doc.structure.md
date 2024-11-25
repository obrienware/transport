# General "Structure" of the application

The application is primarily designed as a single page application. 

Logical separation of sections is implemented using tabs.

Changes in data and notible actions are communicated through events, so each component will be responsible for how it behaves when certain data has been modified.

Event listeners are generally attached to `$(document)` and events are dispatched via `$(document).trigger`.