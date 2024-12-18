import { useState } from "react";
import { Calendar, momentLocalizer, Views } from "react-big-calendar";
import moment from "moment";
import "react-big-calendar/lib/css/react-big-calendar.css";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { CreateEventModal } from "./CreateEventModal";


const localizer = momentLocalizer(moment);

interface Event {
  id: number;
  title: string;
  start: Date;
  end: Date;
  description: string;
  instructor: string;
  eventType: "crossfit" | "specialty" | "open-gym";
}

const eventStyleGetter = (event: Event) => {
  let backgroundColor = "#9333ea"; 
  const color = "#ffffff";

  if (
    event.title.toLowerCase().includes("kettlebell") ||
    event.title.toLowerCase().includes("strength")
  ) {
    backgroundColor = "#0d9488"; 
  } else if (event.title.toLowerCase().includes("open gym")) {
    backgroundColor = "#374151"; 
  }

  return {
    style: {
      backgroundColor,
      color,
      borderRadius: "4px",
      border: "none",
      padding: "4px",
    },
  };
};

const EventComponent = ({ event }: { event: Event }) => (
  <div>
    <div className="font-semibold">{event.title}</div>
    <div className="text-sm">
      {moment(event.start).format("HH:mm")} —{" "}
      {moment(event.end).format("HH:mm")}
    </div>
    <div className="text-sm">{event.instructor}</div>
  </div>
);

export default function CrossfitCalendar() {
  const [events, setEvents] = useState<Event[]>([]);
  const [selectedEvent, setSelectedEvent] = useState<Event | null>(null);
  const [isViewDialogOpen, setIsViewDialogOpen] = useState(false);
  const [isEditMode, setIsEditMode] = useState(false);
  const [editedEvent, setEditedEvent] = useState<Event | null>(null);
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [selectedSlot, setSelectedSlot] = useState<{
    start: Date;
    end: Date;
  } | null>(null);

  const handleSelectSlot = ({ start, end }: { start: Date; end: Date }) => {
    setSelectedSlot({ start, end });
    setIsCreateModalOpen(true);
  };

  const handleSelectEvent = (event: Event) => {
    setSelectedEvent(event);
    setIsViewDialogOpen(true);
  };

  const handleCreateEvent = (eventData: {
    title: string;
    start: Date;
    end: Date;
    description: string;
    instructor: string;
  }) => {
    const newEvent: Event = {
      id: events.length + 1,
      ...eventData,
      eventType: eventData.title.toLowerCase().includes("open gym")
        ? "open-gym"
        : eventData.title.toLowerCase().includes("kettlebell") ||
          eventData.title.toLowerCase().includes("strength")
        ? "specialty"
        : "crossfit",
    };
    setEvents([...events, newEvent]);
  };

  const handleUpdateEvent = () => {
    if (selectedEvent && editedEvent) {
      setEvents((prevEvents) =>
        prevEvents.map((event) =>
          event.id === selectedEvent.id ? editedEvent : event
        )
      );
      setIsViewDialogOpen(false);
      setEditedEvent(null);
    }
  };

  const handleDeleteEvent = () => {
    if (selectedEvent) {
      setEvents((prevEvents) =>
        prevEvents.filter((event) => event.id !== selectedEvent.id)
      );
      setIsViewDialogOpen(false);
    }
  };

  const handleReserve = () => {
    if (selectedEvent) {
      const whatsappNumber =
        crossfitCalendarConfig.whatsappNumber || "000000000"; // Fallback
      const message = encodeURIComponent(
        `Hola, me gustaría reservar el entrenamiento "${
          selectedEvent.title
        }" con ${selectedEvent.instructor} que comienza el ${moment(
          selectedEvent.start
        ).format("DD/MM/YYYY HH:mm")}.`
      );
      window.open(`https://wa.me/${whatsappNumber}?text=${message}`, "_blank");
    }
    setIsViewDialogOpen(false);
  };

  return (
    <div className="h-screen p-4">
      <Calendar
        localizer={localizer}
        events={events}
        startAccessor="start"
        endAccessor="end"
        onSelectSlot={handleSelectSlot}
        onSelectEvent={handleSelectEvent}
        selectable
        step={60}
        timeslots={1}
        defaultView={Views.WEEK}
        min={new Date(0, 0, 0, 5, 0, 0)}
        max={new Date(0, 0, 0, 22, 0, 0)}
        className="rounded-lg shadow-lg"
        eventPropGetter={eventStyleGetter}
        components={{
          event: EventComponent,
        }}
      />

      {selectedSlot && (
        <CreateEventModal
          isOpen={isCreateModalOpen}
          onClose={() => {
            setIsCreateModalOpen(false);
            setSelectedSlot(null);
          }}
          onSubmit={handleCreateEvent}
          initialStart={selectedSlot.start}
          initialEnd={selectedSlot.end}
        />
      )}

      <Dialog open={isViewDialogOpen} onOpenChange={setIsViewDialogOpen}>
        {selectedEvent && (
          <DialogContent>
            <DialogHeader>
              <DialogTitle>{selectedEvent.title}</DialogTitle>
              <DialogDescription>
                {moment(selectedEvent.start).format("DD/MM/YYYY HH:mm")} -{" "}
                {moment(selectedEvent.end).format("HH:mm")}
                <br />
                Instructor: {selectedEvent.instructor}
                {selectedEvent.description && (
                  <>
                    <br />
                    <span className="text-sm text-muted-foreground">
                      {selectedEvent.description}
                    </span>
                  </>
                )}
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button
                onClick={() => {
                  setEditedEvent(selectedEvent);
                  setIsEditMode(true);
                }}
              >
                Editar
              </Button>
              <Button variant="destructive" onClick={handleDeleteEvent}>
                Eliminar
              </Button>
              <Button onClick={handleReserve}>Reservar por WhatsApp</Button>
            </DialogFooter>
          </DialogContent>
        )}
      </Dialog>

      {isEditMode && editedEvent && (
        <CreateEventModal
          isOpen={isEditMode}
          onClose={() => setIsEditMode(false)}
          onSubmit={(data) => {
            setEditedEvent({ ...editedEvent, ...data });
            handleUpdateEvent();
          }}
          initialStart={editedEvent.start}
          initialEnd={editedEvent.end}
        />
      )}
    </div>
  );
}
