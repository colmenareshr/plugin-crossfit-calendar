declare module "react-big-calendar" {
  import * as React from "react";
  import { Moment } from "moment";

  export interface CalendarProps {
    localizer: DateLocalizer;
    events: CalendarEvent[];
    startAccessor: string;
    endAccessor: string;
    onSelectSlot?: (slotInfo: { start: Date; end: Date }) => void;
    onSelectEvent?: (event: CalendarEvent) => void;
    selectable?: boolean;
    step?: number;
    timeslots?: number;
    defaultView?: string;
    min?: Date;
    max?: Date;
    className?: string;
    eventPropGetter?: (event: CalendarEvent) => {
      style?: React.CSSProperties;
      className?: string;
    };
    components?: {
      event?: React.ComponentType<{ event: CalendarEvent }>;
    };
  }

  export class Calendar extends React.Component<CalendarProps> {}

  export interface DateLocalizer {
    format: (date: Date, format: string) => string;
    firstOfWeek: () => number;
    parse: (value: string, format: string) => Date | null;
  }

  export const momentLocalizer: (moment: typeof Moment) => DateLocalizer;

  export const Views: {
    MONTH: string;
    WEEK: string;
    DAY: string;
    AGENDA: string;
  };
}
