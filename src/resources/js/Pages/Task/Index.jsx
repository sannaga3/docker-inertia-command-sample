import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import TaskItem from "@/Components/Task/TaskItem";
import { useState } from "react";
import CreateTaskForm from "@/Components/Task/CreateTaskForm";
import EditTaskModal from "@/Components/Task/EditTaskModal";

export default function Index(props) {
    const tasks = props?.tasks || [];
    const categories = props?.categories || [];

    const [toggleCreateModal, setToggleCreateModal] = useState(false);
    const [editTaskIndex, setEditTaskIndex] = useState(null);

    return (
        <AuthenticatedLayout
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    TaskList
                </h2>
            }
        >
            <Head title="TaskList" />

            <div className="py-12 px-10">
                <button
                    type="button"
                    className="bg-blue-500 text-white rounded-lg px-3 py-1 mb-3 hover:bg-blue-700"
                    onClick={() => setToggleCreateModal(!toggleCreateModal)}
                >
                    createTask
                </button>
                {toggleCreateModal && (
                    <div className="pb-3">
                        <CreateTaskForm
                            categories={categories}
                            setToggleCreateModal={setToggleCreateModal}
                        />
                    </div>
                )}
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg pb-16 mt-3">
                    <div className="grid grid-cols-12 space-x-2 px-3 mx-7 pt-2 pb-1 border-b border-gray-700">
                        <div className="font-semibold ">id</div>
                        <div className="font-semibold col-span-2">date</div>
                        <div className="font-semibold col-span-1">user</div>
                        <div className="font-semibold col-span-1">title</div>
                        <div className="font-semibold col-span-2">content</div>
                        <div className="font-semibold col-span-2">category</div>
                        <div className="font-semibold">finished</div>
                        <div className="font-semibold">edit</div>
                        <div className="font-semibold">delete</div>
                    </div>
                    <div
                        onClick={() => editTaskIndex && setEditTaskIndex(null)}
                    >
                        {tasks && tasks.length > 0 ? (
                            tasks.map((task, index) => (
                                <div key={task.id}>
                                    <TaskItem
                                        task={task}
                                        index={index}
                                        setEditTaskIndex={setEditTaskIndex}
                                    />
                                </div>
                            ))
                        ) : (
                            <div className="flex justify-center mt-5">
                                post not exist
                            </div>
                        )}
                    </div>
                </div>
                {editTaskIndex && (
                    <div className="absolute top-1/3">
                        <EditTaskModal
                            task={tasks[editTaskIndex]}
                            setEditTaskIndex={setEditTaskIndex}
                            categories={categories}
                        />
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
