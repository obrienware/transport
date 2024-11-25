const tempusConfigDefaults = {
  // allowInputToggle: false,
  // container: undefined,
  // dateRange: false,
  // debug: false,
  // defaultDate: undefined,
  display: {
    icons: {
      type: 'icons',
      time: 'fa-duotone fa-clock',
      date: 'fa-duotone fa-calendar',
      up: 'fa-duotone fa-arrow-up',
      down: 'fa-duotone fa-arrow-down',
      previous: 'fa-duotone fa-chevron-left',
      next: 'fa-duotone fa-chevron-right',
      today: 'fa-duotone fa-calendar-check',
      clear: 'fa-duotone fa-trash',
      close: 'fa-duotone fa-xmark'
    },
  //   sideBySide: false,
  //   calendarWeeks: false,
  //   viewMode: 'calendar',
  //   toolbarPlacement: 'bottom',
  //   keepOpen: false,
    buttons: {
      today: true,
      clear: true,
      close: true
    },
  //   components: {
  //     calendar: true,
  //     date: true,
  //     month: true,
  //     year: true,
  //     decades: true,
  //     clock: true,
  //     hours: true,
  //     minutes: true,
  //     seconds: false,
  //     useTwentyfourHour: undefined
  //   },
  //   inline: false,
  //   theme: 'auto'
  },
  // keepInvalid: false,
  // localization: {
  //   today: 'Go to today',
  //   clear: 'Clear selection',
  //   close: 'Close the picker',
  //   selectMonth: 'Select Month',
  //   previousMonth: 'Previous Month',
  //   nextMonth: 'Next Month',
  //   selectYear: 'Select Year',
  //   previousYear: 'Previous Year',
  //   nextYear: 'Next Year',
  //   selectDecade: 'Select Decade',
  //   previousDecade: 'Previous Decade',
  //   nextDecade: 'Next Decade',
  //   previousCentury: 'Previous Century',
  //   nextCentury: 'Next Century',
  //   pickHour: 'Pick Hour',
  //   incrementHour: 'Increment Hour',
  //   decrementHour: 'Decrement Hour',
  //   pickMinute: 'Pick Minute',
  //   incrementMinute: 'Increment Minute',
  //   decrementMinute: 'Decrement Minute',
  //   pickSecond: 'Pick Second',
  //   incrementSecond: 'Increment Second',
  //   decrementSecond: 'Decrement Second',
  //   toggleMeridiem: 'Toggle Meridiem',
  //   selectTime: 'Select Time',
  //   selectDate: 'Select Date',
  //   dayViewHeaderFormat: { month: 'long', year: '2-digit' },
  //   locale: 'default',
  //   startOfTheWeek: 0,
  //   hourCycle: undefined,
  //   dateFormats: {
  //     LTS: 'h:mm:ss T',
  //     LT: 'h:mm T',
  //     L: 'MM/dd/yyyy',
  //     LL: 'MMMM d, yyyy',
  //     LLL: 'MMMM d, yyyy h:mm T',
  //     LLLL: 'dddd, MMMM d, yyyy h:mm T'
  //   },
  //   ordinal: (n) => n,
  //   format: 'L'
  // },
  // meta: {},
  // multipleDates: false,
  // multipleDatesSeparator: '; ',
  // promptTimeOnDateChange: false,
  // promptTimeOnDateChangeTransitionDelay: 200,
  // restrictions: {
  //   minDate: undefined,
  //   maxDate: undefined,
  //   disabledDates: [],
  //   enabledDates: [],
  //   daysOfWeekDisabled: [],
  //   disabledTimeIntervals: [],
  //   disabledHours: [],
  //   enabledHours: []
  // },
  // stepping: 1,
  // useCurrent: true,
  // viewDate: new tempusDominus.DateTime()
  // viewDate: new Date()

}

const dateOnlyDefaults = {
  display: {
    icons: {
      type: 'icons',
      time: 'fa-duotone fa-clock',
      date: 'fa-duotone fa-calendar',
      up: 'fa-duotone fa-arrow-up',
      down: 'fa-duotone fa-arrow-down',
      previous: 'fa-duotone fa-chevron-left',
      next: 'fa-duotone fa-chevron-right',
      today: 'fa-duotone fa-calendar-check',
      clear: 'fa-duotone fa-trash',
      close: 'fa-duotone fa-xmark'
    },
    buttons: {
      today: true,
      // clear: true,
      close: true
    },
    components: {
      calendar: true,
      date: true,
      month: true,
      year: true,
      decades: true,
      clock: false,
      hours: false,
      minutes: false,
      seconds: false,
    },
  },
  localization: {
    format: 'L'
  },
}