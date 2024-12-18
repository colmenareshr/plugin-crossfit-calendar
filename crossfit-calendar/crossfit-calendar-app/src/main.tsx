import { StrictMode } from "@wordpress/element";
import { createRoot } from "@wordpress/element";
import "./index.css";
import App from "./App.tsx";

// Ajusta el ID del elemento raíz según el shortcode
const rootElement = document.getElementById("react-calendar-root");

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(
    <StrictMode>
      <App />
    </StrictMode>
  );
}
