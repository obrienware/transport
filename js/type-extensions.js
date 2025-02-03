String.prototype.toJSON = function () {
  return JSON.parse(this, function(k, v) {
    if (v && typeof v === 'object' && !Array.isArray(v)) {
      return Object.assign(Object.create(null), v);
    }
    return v;
  });
};

String.prototype.toProperCase = function () {
  return this.toLowerCase().replace(/\b((m)(a?c))?(\w)/g,
    function ($1, $2, $3, $4, $5) {
      if ($2) {
        return $3.toUpperCase() + $4 + $5.toUpperCase();
      }
      return $1.toUpperCase();
    }
  );
};

String.prototype.toSentenceCase = function () {
  const sentences = this.split(/([.!?]\s*)/);
  for (let i = 0; i < sentences.length; i += 2) {
    sentences[i] = sentences[i].charAt(0).toUpperCase() + sentences[i].slice(1).toLowerCase();
  }
  return sentences.join('');
};

String.prototype.lpad = function(padString, length) {
  let str = this;
  while (str.length < length)
    str = padString + str;
  return str;
};

String.prototype.rpad = function(padString, length) {
  let str = this;
  let paddingCount = length - str.length;
  if (paddingCount > 0) {
    for (let i = 0; i < paddingCount; i++) {
      str = str + padString;
    }
  }
  return str;
};

Array.prototype.move = function (from, to) {
  this.splice(to, 0, this.splice(from, 1)[0]);
};
