/**
 * CircleAudioPlayer
 *
 * A minimalist audio player represented by a single button in a circle which
 * fills clockwise as the audio file progresses.
 *
 * Isolated to have no requirements.
 *
 * @param {object} options
 *   @prop {string} audio
 *   @prop {string} borderColor
 *   @prop {string} playedColor
 *   @prop {string} backgroundColor
 *   @prop {string} iconColor
 *   @prop {number} borderWidth
 *   @prop {number} size
 *   @prop {string} className - class to add to the canvas element
 *
 * @method {void} addEventListener (planned, not implemented)
 *   forwards to the canvas' addEventListener or the audio element's
 *   addEventListener, depending on whether or not the event string matches a
 *   media event
 *
 * @method {void} setAudio
 *   @param {string} audioUrl
 * @method {void} setSize
 *   @param {number} size
 * @method {void} appendTo
 *   @param {DOMElement} element
 * @method {void} play
 * @method {void} pause
 * @method {void} draw - force a redraw
 */
// requirements

// settings
var DEFAULTS = {
	borderColor: "#7cbd1e",
	playedColor: "#ffffff",
	backgroundColor: "rgba(124,189,30,.2)",
	iconColor: "#ffffff",
	borderWidth: 2,
	size: 48,
	className: 'circle-audio-player'
};

// reused values
var pi = Math.PI;
var doublePi = pi * 2;
var arcOffset = -pi / 2;
var animTime = 200;
var loaderTime = 1800;

var CircleAudioPlayer = function (options) {
	options = options || {};
	for (var property in DEFAULTS) {
		this[property] = options[property] || DEFAULTS[property];
	}

	// create some things we need
	this._canvas = document.createElement('canvas');
	this._canvas.setAttribute('class', this.className + ' is-loading');
	this._canvas.addEventListener('mousedown', (function () {
		if (this.playing) {
			this.pause();
		}
		else {
			this.play();
		}
	}).bind(this));
	this._ctx = this._canvas.getContext('2d');

	// set up initial stuff
	this.setAudio(options.audio);
	this.setSize(this.size);

	// redraw loop
	(function cAPAnimationLoop (now) {
		// check if we need to update anything
		if (this.animating) {
			this._updateAnimations(now);
		}
		if (this._forceDraw || this.playing || this.animating || this.loading) {
			this._draw();
			this._forceDraw = false;
		}

		requestAnimationFrame(cAPAnimationLoop.bind(this));
	}).call(this, new Date().getTime());
};
CircleAudioPlayer.prototype = {
	// private methods
	_animateIcon: function (to, from) {
		// define a few things the first time
		this._animationProps = {
			animStart: null,
			from: from,
			to: to
		};
		if (from) {
			this.animating = true;
		}
		else {
			this._animationProps.current = this._icons[to].slice();
			this.draw();
		}
	},
	_updateAnimations: function (now) {
		this._animationProps.animStart = this._animationProps.animStart || now;
		var deltaTime = now - this._animationProps.animStart;
		var perc = (1 - Math.cos(deltaTime / animTime * pi / 2));
		if (deltaTime >= animTime) {
			this.animating = false;
			perc = 1;
			this._animationProps.current = this._icons[this._animationProps.to].slice();
			this.draw();
		}
		else {
			var from = this._icons[this._animationProps.from];
			var current = [];
			for (var i = 0; i < from.length; i++) {
				current.push([]);
				for (var j = 0; j < from[i].length; j++) {
					current[i].push([]);
					var to = this._icons[this._animationProps.to][i][j];
					current[i][j][0] = from[i][j][0] + (to[0] - from[i][j][0]) * perc;
					current[i][j][1] = from[i][j][1] + (to[1] - from[i][j][1]) * perc;
				}
			}
			this._animationProps.current = current;
		}
	},
	_draw: function (progress) {
		// common settings
		if (isNaN(progress)) {
			progress = this.audio.currentTime / this.audio.duration || 0;
		}

		// clear existing
		this._ctx.clearRect(0, 0, this.size, this.size);

		// draw bg
		this._ctx.beginPath();
		this._ctx.arc(this._halfSize, this._halfSize, this._halfSize - (this.borderWidth / 2), 0, doublePi);
		this._ctx.closePath();
		this._ctx.fillStyle = this.backgroundColor;
		this._ctx.fill();

		// draw border
		// our active path is already the full circle, so just stroke it
		this._ctx.lineWidth = this.borderWidth;
		this._ctx.strokeStyle = this.borderColor;
		this._ctx.stroke();

		// play progress
		if (progress > 0) {
			this._ctx.beginPath();
			this._ctx.arc(this._halfSize, this._halfSize, this._halfSize - (this.borderWidth / 2), arcOffset, arcOffset + doublePi * progress);
			this._ctx.strokeStyle = this.playedColor;
			this._ctx.stroke();
		}

		// icons
		this._ctx.fillStyle = this.iconColor;
		if (this.loading) {
			var loaderOffset = -Math.cos((new Date().getTime() % (loaderTime)) / (loaderTime) * pi) * doublePi - (pi / 3) - (pi / 2);
			this._ctx.beginPath();
			this._ctx.arc(this._halfSize, this._halfSize, this._halfSize / 3, loaderOffset, loaderOffset + pi / 3 * 2);
			this._ctx.strokeStyle = this.iconColor;
			this._ctx.stroke();
		}
		else {
			this._ctx.beginPath();
			var icon = (this._animationProps && this._animationProps.current) || this._icons.play;
			for (var i = 0; i < icon.length; i++) {
				this._ctx.moveTo(icon[i][0][0], icon[i][0][1]);

				for (var j = 1; j < icon[i].length; j++) {
					this._ctx.lineTo(icon[i][j][0], icon[i][j][1]);
				}
			}

			// this._ctx.closePath();
			this._ctx.fill();
			// stroke to fill in for retina
			this._ctx.strokeStyle = this.iconColor;
			this._ctx.lineWidth = 2;
			this._ctx.lineJoin = 'miter';
			this._ctx.stroke();
		}
	},
	_setState: function (state) {
		this.playing = false;
		this.loading = false;
		if (state === 'playing') {
			this.playing = true;
			this._animateIcon('pause', 'play');
		}
		else if (state === 'loading') {
			this.loading = true;
		}
		else if (this.state !== 'loading') {
			this._animateIcon('play', 'pause');
		}
		else {
			this._animateIcon('play', null);
		}
		this.state = state;
		this._canvas.setAttribute('class', this.className + ' is-' + state);
		this.draw();
	},
	// public methods
	draw: function () {
		this._forceDraw = true;
	},
	setSize: function (size) {
		this.size = size;
		this._halfSize = size / 2; // we do this a lot. it's not heavy, but why repeat?
		this._canvas.width = size;
		this._canvas.height = size;
		// set icon paths
		var iconSize = this.size / 2;
		var pauseGap = iconSize / 10;
		var playLeft = Math.cos(pi / 3 * 2) * (iconSize / 2) + this._halfSize;
		var playRight = iconSize / 2 + this._halfSize;
		var playHalf = (playRight - playLeft) / 2 + playLeft;
		var top = this._halfSize - Math.sin(pi / 3 * 2) * (iconSize / 2);
		var bottom = this.size - top;
		var pauseLeft = this._halfSize - iconSize / 3;
		var pauseRight = this.size - pauseLeft;
		this._icons = {
			play: [
				[
					[playLeft, top],
					[playHalf, (this._halfSize - top) / 2 + top],
					[playHalf, (this._halfSize - top) / 2 + this._halfSize],
					[playLeft, bottom]
				],
				[
					[playHalf, (this._halfSize - top) / 2 + top],
					[playRight, this._halfSize],
					[playRight, this._halfSize],
					[playHalf, (this._halfSize - top) / 2 + this._halfSize]
				]
			],
			pause: [
				[
					[pauseLeft, top + pauseGap],
					[this._halfSize - pauseGap, top + pauseGap],
					[this._halfSize - pauseGap, bottom - pauseGap],
					[pauseLeft, bottom - pauseGap]
				],
				[
					[this._halfSize + pauseGap, top + pauseGap],
					[pauseRight, top + pauseGap],
					[pauseRight, bottom - pauseGap],
					[this._halfSize + pauseGap, bottom - pauseGap]
				]
			]
		};

		if (this._animationProps && this._animationProps.current) {
			this._animateIcon(this._animationProps.to);
		}
		if (!this.playing) {
			this.draw();
		}
	},
	setAudio: function (audioUrl) {
		this.audio = new Audio(audioUrl);
		this._setState('loading');

		this.audio.addEventListener('canplaythrough', (function () {
			this._setState('paused');
		}).bind(this));
		this.audio.addEventListener('play', (function () {
			this._setState('playing');
		}).bind(this));
		this.audio.addEventListener('pause', (function () {
			// reset when finished
			if (this.audio.currentTime === this.audio.duration) {
				this.audio.currentTime = 0;
			}
			this._setState('paused');
		}).bind(this));
	},
	appendTo: function (element) {
		element.appendChild(this._canvas);
	},
	play: function () {
		this.audio.play();
	},
	pause: function () {
		this.audio.pause();
	}
};


// now init one as an example
var cap = new CircleAudioPlayer({
	audio: 'http://www.largesound.com/ashborytour/sound/AshboryBYU.mp3',
  size: 120,
  borderWidth: 4
});
cap.appendTo(playerContainer);