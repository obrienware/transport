const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

const uuidv4 = () => {
  return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
    (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
  );
};

// The function returns the calculated luminance value, which ranges from 0 (black) to 1 (white).
function luminance(r, g, b) {
  // Convert RGB values to the range 0-1
  r /= 255;
  g /= 255;
  b /= 255;

  // Apply the luminance formula
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function hexToRgba(hex, alpha) {
  // Remove the hash at the start if it's there
  hex = hex.replace(/^#/, '');

  // Parse the r, g, b values
  let r = parseInt(hex.substring(0, 2), 16);
  let g = parseInt(hex.substring(2, 4), 16);
  let b = parseInt(hex.substring(4, 6), 16);

  // Return the RGBA color
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}


function luminanceColor(hexColor) {
 // Remove the hash at the start if it's there
 hexColor = hexColor.replace(/^#/, '');

 // Convert hex to RGB
 const r = parseInt(hexColor.substring(0, 2), 16);
 const g = parseInt(hexColor.substring(2, 4), 16);
 const b = parseInt(hexColor.substring(4, 6), 16);

 // Calculate brightness (standard luminance formula)
 const brightness = (r * 299 + g * 587 + b * 114) / 1000;

 // Return black or white depending on brightness
 return brightness > 128 ? 'black' : 'white';
}



export { wait, uuidv4, luminance, hexToRgba, luminanceColor };