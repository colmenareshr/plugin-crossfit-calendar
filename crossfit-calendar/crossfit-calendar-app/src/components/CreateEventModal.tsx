import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { useState } from "react";

interface CreateEventModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (eventData: {
    title: string;
    start: Date;
    end: Date;
    description: string;
    instructor: string;
  }) => void;
  initialStart: Date;
  initialEnd: Date;
}

export function CreateEventModal({
  isOpen,
  onClose,
  onSubmit,
  initialStart,
  initialEnd,
}: CreateEventModalProps) {
  const [title, setTitle] = useState("");
  const [start, setStart] = useState(initialStart);
  const [end, setEnd] = useState(initialEnd);
  const [description, setDescription] = useState("");
  const [instructor, setInstructor] = useState("");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit({
      title,
      start,
      end,
      description,
      instructor,
    });
    setTitle("");
    setDescription("");
    setInstructor("");
    onClose();
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Crear Nuevo Entrenamiento</DialogTitle>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="title">Nombre del Entrenamiento</Label>
            <Input
              id="title"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="CrossFit"
              required
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="start">Hora de Inicio</Label>
              <Input
                id="start"
                type="time"
                value={start.toTimeString().slice(0, 5)}
                onChange={(e) => {
                  const [hours, minutes] = e.target.value.split(":");
                  const newDate = new Date(start);
                  newDate.setHours(parseInt(hours), parseInt(minutes));
                  setStart(newDate);
                }}
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="end">Hora de Fin</Label>
              <Input
                id="end"
                type="time"
                value={end.toTimeString().slice(0, 5)}
                onChange={(e) => {
                  const [hours, minutes] = e.target.value.split(":");
                  const newDate = new Date(end);
                  newDate.setHours(parseInt(hours), parseInt(minutes));
                  setEnd(newDate);
                }}
                required
              />
            </div>
          </div>
          <div className="space-y-2">
            <Label htmlFor="instructor">Instructor</Label>
            <Input
              id="instructor"
              value={instructor}
              onChange={(e) => setInstructor(e.target.value)}
              placeholder="Nombre del instructor"
              required
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="description">Descripción</Label>
            <Textarea
              id="description"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              placeholder="Detalles del entrenamiento..."
            />
          </div>
          <DialogFooter>
            <Button type="submit">Crear Entrenamiento</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
